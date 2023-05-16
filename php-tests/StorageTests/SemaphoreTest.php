<?php

namespace StorageTests;


use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Storage;
use kalanis\kw_semaphore\Semaphore;
use kalanis\kw_semaphore\SemaphoreException;
use kalanis\kw_storage\Interfaces\IStorage;


class SemaphoreTest extends AStorageTest
{
    /**
     * @throws CacheException
     * @throws SemaphoreException
     */
    public function testRun(): void
    {
        $storage = $this->getStorage(new \MockStorage());
        $semaphore = new Semaphore\Storage($storage, 'dummy');
        $lib = new Storage\Semaphore($storage, $semaphore);
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
     */
    public function testNotSet(): void
    {
        $storage = $this->getStorage(new \MockFailedStorage());
        $semaphore = new Semaphore\Storage($storage, 'dummy');
        $lib = new Storage\Semaphore($storage, $semaphore);
        $lib->init(['']);

        $this->assertFalse($lib->exists());
        $this->assertEquals('', $lib->get());
        $this->assertFalse($lib->set(static::TESTING_CONTENT));
        $this->assertFalse($lib->exists());
    }

    /**
     * @throws CacheException
     */
    public function testFailExists(): void
    {
        $storage = $this->getStorage(new \MockKillingStorage());
        $semaphore = new Semaphore\Storage($storage, 'dummy');
        $lib = new Storage\Semaphore($storage, $semaphore);

        $this->expectException(CacheException::class);
        $lib->exists();
    }

    /**
     * @throws CacheException
     */
    public function testCannotSet(): void
    {
        $storage = $this->getStorage(new \MockKillingStorage2());
        $lib = new Storage\Semaphore($storage, new \MockSemaphore());
        $lib->init(['']);
        $this->expectException(CacheException::class);
        $lib->set('lkjhgfdsa');
    }

    /**
     * @throws CacheException
     */
    public function testNotGet(): void
    {
        $storage = $this->getStorage(new \MockKillingStorage2());
        $lib = new Storage\Semaphore($storage, new \MockSemaphore());
        $lib->init(['']);
        $this->expectException(CacheException::class);
        $lib->get();
    }

    /**
     * @throws CacheException
     */
    public function testNotClear(): void
    {
        $storage = $this->getStorage(new \MockKillingStorage2());
        $lib = new Storage\Semaphore($storage, new \MockSemaphore());
        $lib->init(['']);
        $this->expectException(CacheException::class);
        $lib->clear();
    }

    /**
     * @throws CacheException
     * @throws SemaphoreException
     */
    public function testFailSet(): void
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
}


class SemaphoreStorage extends Semaphore\Storage
{
    public function setStorage(IStorage $storage): void
    {
        $this->storage = $storage;
    }
}
