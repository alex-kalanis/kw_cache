<?php

namespace CacheTests;


use kalanis\kw_cache\Cache\Formatted;
use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Format\Raw;
use kalanis\kw_cache\Simple;


class FormattedTest extends ACacheTest
{
    /**
     * @throws CacheException
     */
    public function testRun(): void
    {
        $lib = new Formatted(new Simple\Variable(), new Raw());
        $lib->init(['']);

        $this->assertFalse($lib->exists());
        $this->assertEquals('', $lib->get());
        $this->assertTrue($lib->set(static::TESTING_CONTENT));
        $this->assertTrue($lib->exists());
        $this->assertEquals(static::TESTING_CONTENT, $lib->get());
        $lib->clear();
        $this->assertFalse($lib->exists());
    }
}
