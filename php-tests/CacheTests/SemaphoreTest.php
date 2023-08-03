<?php

namespace CacheTests;


use kalanis\kw_cache\Cache;
use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Simple;
use kalanis\kw_semaphore\Semaphore;
use kalanis\kw_semaphore\SemaphoreException;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;


class SemaphoreTest extends ACacheTest
{
    /**
     * @throws CacheException
     * @throws SemaphoreException
     * @throws StorageException
     */
    public function testRun(): void
    {
        $semaphore = new Semaphore\Storage($this->getStorage(new \MockStorage()), 'dummy');
        $lib = new Cache\Semaphore(new Simple\Variable(), $semaphore);
        $lib->init(['']);

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
     * @throws StorageException
     */
    public function testNotSet(): void
    {
        $semaphore = new Semaphore\Storage($this->getStorage(new \MockFailedStorage()), 'dummy');
        $lib = new Cache\Semaphore(new MockCacheFail(), $semaphore);
        $lib->init(['']);

        $this->assertFalse($lib->exists());
        $this->assertEquals('', $lib->get());
        $this->assertFalse($lib->set(static::TESTING_CONTENT));
        $this->assertFalse($lib->exists());
    }

    /**
     * @throws CacheException
     * @throws StorageException
     */
    public function testFailExists(): void
    {
        $semaphore = new Semaphore\Storage($this->getStorage(new \MockKillingStorage()), 'dummy');
        $storage = new Simple\Variable();
        $lib = new Cache\Semaphore($storage, $semaphore);

        $storage->set(static::TESTING_CONTENT);
        $this->expectException(CacheException::class);
        $lib->exists();
    }

    /**
     * @throws CacheException
     * @throws SemaphoreException
     * @throws StorageException
     */
    public function testFailSet(): void
    {
        $semaphore = new SemaphoreCache($this->getStorage(new \MockStorage()), 'dummy');
        $lib = new Cache\Semaphore(new Simple\Variable(), $semaphore);

        $this->assertTrue($lib->set(static::TESTING_CONTENT));
        $this->assertTrue($lib->exists());
        $this->assertEquals(static::TESTING_CONTENT, $lib->get());

        // semaphore action
        $semaphore->want();
        $semaphore->setStorage($this->getStorage(new \MockKillingStorage()));
        $this->expectException(CacheException::class);
        $lib->set(static::TESTING_CONTENT . static::TESTING_CONTENT);
    }
}


class SemaphoreCache extends Semaphore\Storage
{
    public function setStorage(IStorage $storage): void
    {
        $this->storage = $storage;
    }
}
