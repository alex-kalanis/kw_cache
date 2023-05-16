<?php

namespace BasicTests;


use kalanis\kw_cache\Simple;
use kalanis\kw_cache\StaticCache;
use kalanis\kw_cache\CacheException;


class StaticTest extends \CommonTestClass
{
    /**
     * @throws CacheException
     */
    public function testRun(): void
    {
        StaticCache::setCache(new Simple\Memory());
        $this->assertNotEmpty(StaticCache::getCache());
        StaticCache::init('');

        $this->assertFalse(StaticCache::exists());
        $this->assertEquals('', StaticCache::get());
        // simple write
        $this->assertTrue(StaticCache::set(static::TESTING_CONTENT));
        $this->assertTrue(StaticCache::exists());
        $this->assertEquals(static::TESTING_CONTENT, StaticCache::get());
        // clear all
        StaticCache::clear();
        $this->assertFalse(StaticCache::exists());
    }

    /**
     * @throws CacheException
     */
    public function testFailExists(): void
    {
        StaticCache::setCache(null);
        $this->expectException(CacheException::class);
        StaticCache::getCache();
    }
}
