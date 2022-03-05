<?php

namespace StorageTests;


use kalanis\kw_cache\Cache;
use kalanis\kw_cache\Interfaces\ICache;
use kalanis\kw_cache\Simple;
use kalanis\kw_semaphore\Semaphore;
use kalanis\kw_semaphore\SemaphoreException;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;
use kalanis\kw_storage\Storage as XStorage;


class CacheTest extends \CommonTestClass
{
    /**
     * @throws SemaphoreException
     * @throws StorageException
     */
    public function testSemaphore(): void
    {
        $semaphore = new Semaphore\Storage($this->getStorage(new \MockStorage()), 'dummy');
        $lib = new Cache\Semaphore(new Simple\Variable(), $semaphore);
        $lib->init('');

        $this->assertFalse($lib->exists());
        $this->assertEquals('', $lib->get());
        // simple write
        $this->assertTrue($lib->set(static::TESTING_CONTENT));
        $this->assertTrue($lib->exists());
        $this->assertEquals(static::TESTING_CONTENT, $lib->get());
        // retry reload
        $semaphore->want();
        $this->assertTrue($lib->set(static::TESTING_CONTENT . static::TESTING_CONTENT));
        // clear all
        $lib->clear();
        $this->assertFalse($lib->exists());
    }

    /**
     * @throws StorageException
     */
    public function testSemaphoreNotSet(): void
    {
        $semaphore = new Semaphore\Storage($this->getStorage(new \MockFailedStorage()), 'dummy');
        $lib = new Cache\Semaphore(new MockCacheFail(), $semaphore);
        $lib->init('');

        $this->assertFalse($lib->exists());
        $this->assertEquals('', $lib->get());
        $this->assertFalse($lib->set(static::TESTING_CONTENT));
        $this->assertFalse($lib->exists());
    }

    /**
     * @throws StorageException
     */
    public function testSemaphoreFailExists(): void
    {
        $semaphore = new Semaphore\Storage($this->getStorage(new \MockKillingStorage()), 'dummy');
        $storage = new Simple\Variable();
        $lib = new Cache\Semaphore($storage, $semaphore);

        $storage->set(static::TESTING_CONTENT);
        $this->expectException(StorageException::class);
        $lib->exists();
    }

    /**
     * @throws SemaphoreException
     * @throws StorageException
     */
    public function testSemaphoreFailSet(): void
    {
        $semaphore = new SemaphoreCache($this->getStorage(new \MockStorage()), 'dummy');
        $lib = new Cache\Semaphore(new Simple\Variable(), $semaphore);

        $this->assertTrue($lib->set(static::TESTING_CONTENT));
        $this->assertTrue($lib->exists());
        $this->assertEquals(static::TESTING_CONTENT, $lib->get());

        // semaphore action
        $semaphore->want();
        $semaphore->setStorage($this->getStorage(new \MockKillingStorage()));
        $this->expectException(StorageException::class);
        $lib->set(static::TESTING_CONTENT . static::TESTING_CONTENT);
    }

    /**
     * @throws StorageException
     */
    public function testDual(): void
    {
        $storage = new Simple\Variable();
        $lib = new Cache\Dual(new Simple\Variable(), $storage);
        $lib->init('');

        $this->assertFalse($lib->exists());
        $this->assertEquals('', $lib->get());
        // simple write
        $this->assertTrue($lib->set(static::TESTING_CONTENT));
        $this->assertEquals(static::TESTING_CONTENT, $lib->get());
        $this->assertTrue($lib->exists());
        // retry reload
        $storage->set('DUMMY'); // new reload key
        $this->assertTrue($lib->set(static::TESTING_CONTENT . static::TESTING_CONTENT));
        // clear all
        $lib->clear();
        $this->assertFalse($lib->exists());
    }

    /**
     * @throws StorageException
     */
    public function testDualNotSet(): void
    {
        $lib = new CacheDual(new MockCacheFail());
        $lib->init('');

        $this->assertFalse($lib->exists());
        $this->assertEquals('', $lib->get());
        $this->assertFalse($lib->set(static::TESTING_CONTENT));
        $this->assertFalse($lib->exists());
    }

    /**
     * @throws StorageException
     */
    public function testDualFailSet(): void
    {
        $lib = new Cache\Dual(new Simple\Variable(), new MockCacheKill());
        $this->expectException(StorageException::class);
        $lib->set(static::TESTING_CONTENT . static::TESTING_CONTENT);
    }

    protected function getStorage(IStorage $mockStorage): XStorage\Storage
    {
        XStorage\Key\DirKey::setDir(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR);
        $storage = new XStorage\Factory(new XStorage\Target\Factory(), new XStorage\Format\Factory(), new XStorage\Key\Factory());
        return $storage->getStorage($mockStorage);
    }
}


class SemaphoreCache extends Semaphore\Storage
{
    public function setStorage(XStorage\Storage $storage): void
    {
        $this->storage = $storage;
    }
}


class CacheDual extends Cache\Dual
{
    public function setReload(ICache $reloadCache): void
    {
        $this->reloadCache = $reloadCache;
    }
}


class MockCacheFail implements ICache
{
    public function init(string $what): void
    {
    }

    public function exists(): bool
    {
        return false;
    }

    public function set(string $content): bool
    {
        return false;
    }

    public function get(): string
    {
        return '';
    }

    public function clear(): void
    {
    }
}

class MockCacheKill implements ICache
{
    public function init(string $what): void
    {
    }

    public function exists(): bool
    {
        return true;
    }

    public function set(string $content): bool
    {
        return true;
    }

    public function get(): string
    {
        return '';
    }

    public function clear(): void
    {
        throw new StorageException('mock test');
    }
}