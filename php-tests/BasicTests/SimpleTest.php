<?php

namespace BasicTests;


use kalanis\kw_cache\Simple;
use kalanis\kw_storage\StorageException;


class SimpleTest extends \CommonTestClass
{
    /**
     * @throws StorageException
     */
    public function testVariable(): void
    {
        $lib = new Simple\Variable();
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
     * @throws StorageException
     */
    public function testMemory(): void
    {
        $lib = new Simple\Memory();
        $lib->init('');

        $this->assertFalse($lib->exists());
        $this->assertEquals('', $lib->get());
        $this->assertTrue($lib->set(static::TESTING_CONTENT));
        $this->assertTrue($lib->exists());
        $this->assertEquals(static::TESTING_CONTENT, $lib->get());
        $lib->clear();
        $this->assertFalse($lib->exists());
    }
}
