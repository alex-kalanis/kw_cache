<?php

namespace StorageTests;


use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Interfaces\ICache;
use kalanis\kw_cache\Storage as CStor;
use kalanis\kw_storage\Storage;
use kalanis\kw_storage\StorageException;


class DualTest extends AStorageTest
{
    /**
     * @throws CacheException
     * @throws StorageException
     */
    public function testRun(): void
    {
        $storage = $this->getStorage(new \MockStorage());
        $lib = new CStor\Dual($storage, $storage);
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
    public function testNotSet(): void
    {
        $lib = new CStor\Dual($this->getStorage(new \MockFailedStorage()));
        $lib->init('');

        $this->assertFalse($lib->exists());
        $this->assertEquals('', $lib->get());
        $this->assertFalse($lib->set(static::TESTING_CONTENT));
        $this->assertFalse($lib->exists());
    }

    /**
     * @throws CacheException
     */
    public function testCannotSet(): void
    {
        $lib = new CStor\Dual($this->getStorage(new \MockKillingStorage2()));
        $lib->init('');
        $this->expectException(CacheException::class);
        $lib->set('lkjhgfdsa');
    }

    /**
     * @throws CacheException
     */
    public function testNotGet(): void
    {
        $lib = new CStor\Dual($this->getStorage(new \MockKillingStorage2()), $this->getStorage(new \MockFailedStorage()));
        $lib->init('');
        $this->expectException(CacheException::class);
        $lib->get();
    }

    /**
     * @throws CacheException
     */
    public function testNotClear(): void
    {
        $lib = new CStor\Dual($this->getStorage(new \MockKillingStorage2()));
        $lib->init('');
        $this->expectException(CacheException::class);
        $lib->clear();
    }

    public function testCompare(): void
    {
        // one target
        $lib = new CompareDual($this->getStorage(new \MockStorage()));
        $this->assertEquals($lib->getStorage(), $lib->getReload());

        // multiple targets
        $lib = new CompareDual($this->getStorage(new \MockStorage()), $this->getStorage('volume'));
        $this->assertNotEquals($lib->getStorage(), $lib->getReload());
    }
}


class CompareDual extends CStor\Dual
{
    public function getStorage(): Storage\Storage
    {
        return $this->cacheStorage;
    }

    public function getReload(): Storage\Storage
    {
        return $this->reloadStorage;
    }
}
