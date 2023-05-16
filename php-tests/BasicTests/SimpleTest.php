<?php

namespace BasicTests;


use kalanis\kw_cache\Simple;
use kalanis\kw_cache\CacheException;


class SimpleTest extends \CommonTestClass
{
    /**
     * @throws CacheException
     */
    public function testVariable(): void
    {
        $lib = new Simple\Variable();
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
     */
    public function testMemory(): void
    {
        $lib = new Simple\Memory();
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
