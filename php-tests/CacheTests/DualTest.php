<?php

namespace CacheTests;


use kalanis\kw_cache\Cache;
use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Interfaces\ICache;
use kalanis\kw_cache\Simple;


class DualTest extends ACacheTest
{
    /**
     * @throws CacheException
     */
    public function testRun(): void
    {
        $storage = new Simple\Variable();
        $lib = new Cache\Dual(new Simple\Variable(), $storage);
        $lib->init(['']);

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
     * @throws CacheException
     */
    public function testNotSet(): void
    {
        $lib = new CacheDual(new MockCacheFail());
        $lib->init(['']);

        $this->assertFalse($lib->exists());
        $this->assertEquals('', $lib->get());
        $this->assertFalse($lib->set(static::TESTING_CONTENT));
        $this->assertFalse($lib->exists());
    }

    /**
     * @throws CacheException
     */
    public function testFailSet(): void
    {
        $lib = new Cache\Dual(new Simple\Variable(), new MockCacheKill());
        $this->expectException(CacheException::class);
        $lib->set(static::TESTING_CONTENT . static::TESTING_CONTENT);
    }

    public function testCompare(): void
    {
        // one target
        $lib = new CompareDual(new Simple\Memory());
        $this->assertEquals($lib->getStorage(), $lib->getReload());

        // multiple targets
        $lib = new CompareDual(new Simple\Memory(), new Simple\Variable());
        $this->assertNotEquals($lib->getStorage(), $lib->getReload());
    }
}


class CacheDual extends Cache\Dual
{
    public function setReload(ICache $reloadCache): void
    {
        $this->reloadCache = $reloadCache;
    }
}


class CompareDual extends Cache\Dual
{
    public function getStorage(): ICache
    {
        return $this->storageCache;
    }

    public function getReload(): ICache
    {
        return $this->reloadCache;
    }
}
