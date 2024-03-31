<?php

namespace FilesTests;


use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Files;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\FilesException;
use kalanis\kw_paths\PathsException;
use kalanis\kw_semaphore\Semaphore;
use kalanis\kw_semaphore\SemaphoreException;
use kalanis\kw_storage\StorageException;


class SemaphoreTest extends AFilesTest
{
    /**
     * @throws CacheException
     * @throws FilesException
     * @throws PathsException
     * @throws SemaphoreException
     * @throws StorageException
     */
    public function testRun(): void
    {
        $storage = $this->getStorage(new \MockStorage());
        $semaphore = new Semaphore\Files($storage, ['dummy']);
        $lib = new Files\Semaphore($storage, $semaphore);
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
     * @throws FilesException
     * @throws PathsException
     * @throws StorageException
     */
    public function testNotSet(): void
    {
        $storage = $this->getStorage(new \MockFailedStorage());
        $semaphore = new Semaphore\Files($storage, ['dummy']);
        $lib = new Files\Semaphore($storage, $semaphore);
        $lib->init(['']);

        $this->assertFalse($lib->exists());
        $this->assertEquals('', $lib->get());
        $this->assertFalse($lib->set(static::TESTING_CONTENT));
        $this->assertFalse($lib->exists());
    }

    /**
     * @throws CacheException
     * @throws FilesException
     * @throws PathsException
     * @throws StorageException
     */
    public function testFailExists(): void
    {
        $storage = $this->getStorage(new \MockKillingStorage());
        $semaphore = new Semaphore\Files($storage, ['dummy']);
        $lib = new Files\Semaphore($storage, $semaphore);

        $this->expectException(CacheException::class);
        $lib->exists();
    }

    /**
     * @throws CacheException
     * @throws FilesException
     * @throws PathsException
     * @throws StorageException
     */
    public function testCannotSet(): void
    {
        $storage = $this->getStorage(new \MockKillingStorage2());
        $lib = new Files\Semaphore($storage, new \MockSemaphore());
        $lib->init(['']);
        $this->expectException(CacheException::class);
        $lib->set('lkjhgfdsa');
    }

    /**
     * @throws CacheException
     * @throws FilesException
     * @throws PathsException
     * @throws StorageException
     */
    public function testNotGet(): void
    {
        $storage = $this->getStorage(new \MockKillingStorage2());
        $lib = new Files\Semaphore($storage, new \MockSemaphore());
        $lib->init(['']);
        $this->expectException(CacheException::class);
        $lib->get();
    }

    /**
     * @throws CacheException
     * @throws FilesException
     * @throws PathsException
     * @throws StorageException
     */
    public function testNotClear(): void
    {
        $storage = $this->getStorage(new \MockKillingStorage2());
        $lib = new Files\Semaphore($storage, new \MockSemaphore());
        $lib->init(['']);
        $this->expectException(CacheException::class);
        $lib->clear();
    }

    /**
     * @throws CacheException
     * @throws FilesException
     * @throws PathsException
     * @throws SemaphoreException
     * @throws StorageException
     */
    public function testFailSet(): void
    {
        $semaphore = new SemaphoreFiles($this->getStorage(new \MockStorage()), ['dummy']);
        $lib = new Files\Semaphore($this->getStorage(new \MockStorage()), $semaphore);

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


class SemaphoreFiles extends Semaphore\Files
{
    public function setStorage(CompositeAdapter $lib): void
    {
        $this->lib = $lib;
    }
}
