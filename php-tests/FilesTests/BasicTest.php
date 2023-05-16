<?php

namespace FilesTests;


use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Files;
use kalanis\kw_files\FilesException;
use kalanis\kw_paths\PathsException;


class BasicTest extends AFilesTest
{
    /**
     * @throws CacheException
     * @throws FilesException
     * @throws PathsException
     */
    public function testRun(): void
    {
        $lib = new Files\Basic($this->getStorage(new \MockStorage()));
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
     * @throws FilesException
     * @throws PathsException
     */
    public function testNotExists(): void
    {
        $lib = new Files\Basic($this->getStorage(new \MockKillingStorage4()));
        $lib->init('');
        $this->expectException(CacheException::class);
        $lib->exists();
    }

    /**
     * @throws CacheException
     * @throws FilesException
     * @throws PathsException
     */
    public function testNotSet(): void
    {
        $lib = new Files\Basic($this->getStorage(new \MockKillingStorage2()));
        $lib->init('');
        $this->expectException(CacheException::class);
        $lib->set('lkjhgfdsa');
    }

    /**
     * @throws CacheException
     * @throws FilesException
     * @throws PathsException
     */
    public function testNotGet(): void
    {
        $lib = new Files\Basic($this->getStorage(new \MockKillingStorage2()));
        $lib->init('');
        $this->expectException(CacheException::class);
        $lib->get();
    }

    /**
     * @throws CacheException
     * @throws FilesException
     * @throws PathsException
     */
    public function testNotClear(): void
    {
        $lib = new Files\Basic($this->getStorage(new \MockKillingStorage2()));
        $lib->init('');
        $this->expectException(CacheException::class);
        $lib->clear();
    }
}
