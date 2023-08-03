<?php

namespace StorageTests;


use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Storage;
use kalanis\kw_storage\StorageException;


class BasicTest extends AStorageTest
{
    /**
     * @throws CacheException
     * @throws StorageException
     */
    public function testRun(): void
    {
        $lib = new Storage\Basic($this->getStorage(new \MockStorage()));
        $lib->init(['']);

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
     * @throws StorageException
     */
    public function testNotExists(): void
    {
        $lib = new Storage\Basic($this->getStorage(new \MockKillingStorage3()));
        $lib->init(['']);
        $this->expectException(CacheException::class);
        $lib->exists();
    }

    /**
     * @throws CacheException
     * @throws StorageException
     */
    public function testNotSet(): void
    {
        $lib = new Storage\Basic($this->getStorage(new \MockKillingStorage2()));
        $lib->init(['']);
        $this->expectException(CacheException::class);
        $lib->set('lkjhgfdsa');
    }

    /**
     * @throws CacheException
     * @throws StorageException
     */
    public function testNotGet(): void
    {
        $lib = new Storage\Basic($this->getStorage(new \MockKillingStorage2()));
        $lib->init(['']);
        $this->expectException(CacheException::class);
        $lib->get();
    }

    /**
     * @throws CacheException
     * @throws StorageException
     */
    public function testNotClear(): void
    {
        $lib = new Storage\Basic($this->getStorage(new \MockKillingStorage2()));
        $lib->init(['']);
        $this->expectException(CacheException::class);
        $lib->clear();
    }
}
