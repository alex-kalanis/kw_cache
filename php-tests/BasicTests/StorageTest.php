<?php

namespace StorageTests;


use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Interfaces\ICache;
use kalanis\kw_cache\Storage;
use kalanis\kw_semaphore\Semaphore;
use kalanis\kw_semaphore\SemaphoreException;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\Storage as XStorage;


class StorageTest extends \CommonTestClass
{
    /**
     * @throws CacheException
     */
    public function testBasic(): void
    {
        $lib = new Storage\Basic($this->getStorage(new \MockStorage()));
        $lib->init('');

        $this->assertFalse($lib->exists());
        $this->assertEquals('', $lib->get());
        $this->assertTrue($lib->set(static::TESTING_CONTENT));
        $this->assertTrue($lib->exists());
        $this->assertEquals(static::TESTING_CONTENT, $lib->get());
        $lib->clear();
        $this->assertFalse($lib->exists());
    }

    /**
     * @throws CacheException
     * @throws SemaphoreException
     */
    public function testSemaphore(): void
    {
        $storage = $this->getStorage(new \MockStorage());
        $semaphore = new Semaphore\Storage($storage, 'dummy');
        $lib = new Storage\Semaphore($storage, $semaphore);
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
     * @throws CacheException
     */
    public function testSemaphoreNotSet(): void
    {
        $storage = $this->getStorage(new \MockFailedStorage());
        $semaphore = new Semaphore\Storage($storage, 'dummy');
        $lib = new Storage\Semaphore($storage, $semaphore);
        $lib->init('');

        $this->assertFalse($lib->exists());
        $this->assertEquals('', $lib->get());
        $this->assertFalse($lib->set(static::TESTING_CONTENT));
        $this->assertFalse($lib->exists());
    }

    /**
     * @throws CacheException
     */
    public function testSemaphoreFailExists(): void
    {
        $storage = $this->getStorage(new \MockKillingStorage());
        $semaphore = new Semaphore\Storage($storage, 'dummy');
        $lib = new Storage\Semaphore($storage, $semaphore);

        $this->expectException(CacheException::class);
        $lib->exists();
    }

    /**
     * @throws CacheException
     * @throws SemaphoreException
     */
    public function testSemaphoreFailSet(): void
    {
        $semaphore = new SemaphoreStorage($this->getStorage(new \MockStorage()), 'dummy');
        $lib = new Storage\Semaphore($this->getStorage(new \MockStorage()), $semaphore);

        $this->assertTrue($lib->set(static::TESTING_CONTENT));
        $this->assertTrue($lib->exists());
        $this->assertEquals(static::TESTING_CONTENT, $lib->get());

        // semaphore action
        $semaphore->want();
        $semaphore->setStorage($this->getStorage(new \MockKillingStorage()));
        $this->expectException(CacheException::class);
        $lib->set(static::TESTING_CONTENT . static::TESTING_CONTENT);
    }

    /**
     * @throws CacheException
     */
    public function testDual(): void
    {
        $storage = $this->getStorage(new \MockStorage());
        $lib = new Storage\Dual($storage, $storage);
        $lib->init('');

        $this->assertFalse($lib->exists());
        $this->assertEquals('', $lib->get());
        // simple write
        $this->assertTrue($lib->set(static::TESTING_CONTENT));
        $this->assertEquals(static::TESTING_CONTENT, $lib->get());
        $this->assertTrue($lib->exists());
        // retry reload
        $storage->write(ICache::EXT_RELOAD, 'DUMMY'); // new reload key
        $this->assertTrue($lib->set(static::TESTING_CONTENT . static::TESTING_CONTENT));
        // clear all
        $lib->clear();
        $this->assertFalse($lib->exists());
    }

    /**
     * @throws CacheException
     */
    public function testDualNotSet(): void
    {
        $lib = new Storage\Dual($this->getStorage(new \MockFailedStorage()));
        $lib->init('');

        $this->assertFalse($lib->exists());
        $this->assertEquals('', $lib->get());
        $this->assertFalse($lib->set(static::TESTING_CONTENT));
        $this->assertFalse($lib->exists());
    }

    protected function getStorage(IStorage $mockStorage): XStorage\Storage
    {
        XStorage\Key\DirKey::setDir(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR);
        $storage = new XStorage\Factory(new XStorage\Key\Factory(), new XStorage\Target\Factory());
        return $storage->getStorage($mockStorage);
    }
}


class SemaphoreStorage extends Semaphore\Storage
{
    public function setStorage(XStorage\Storage $storage): void
    {
        $this->storage = $storage;
    }
}
