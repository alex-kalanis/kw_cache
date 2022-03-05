<?php

namespace StorageTests;


use kalanis\kw_cache\Interfaces\ICache;
use kalanis\kw_cache\Storage;
use kalanis\kw_semaphore\Semaphore;
use kalanis\kw_semaphore\SemaphoreException;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;
use kalanis\kw_storage\Storage as XStorage;


class StorageTest extends \CommonTestClass
{
    /**
     * @throws StorageException
     */
    public function testBasic(): void
    {
        $lib = new Storage\Basic($this->getStorage(new \MockStorage()));
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
    public function testSemaphore(): void
    {
        $storage = $this->getStorage(new \MockStorage());
        $semaphore = new Semaphore\Storage($storage, 'dummy');
        $lib = new Storage\Semaphore($storage, $semaphore);
        $lib->init('');

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
     * @throws StorageException
     */
    public function testSemaphoreNotSet(): void
    {
        $storage = $this->getStorage(new \MockFailedStorage());
        $semaphore = new Semaphore\Storage($storage, 'dummy');
        $lib = new Storage\Semaphore($storage, $semaphore);
        $lib->init('');

        $this->assertFalse($lib->exists());
        $this->assertEquals('', $lib->get());
        $this->assertFalse($lib->set(static::TESTING_CONTENT));
        $this->assertFalse($lib->exists());
    }

    /**
     * @throws StorageException
     */
    public function testSemaphoreFailExists(): void
    {
        $storage = $this->getStorage(new \MockKillingStorage());
        $semaphore = new Semaphore\Storage($storage, 'dummy');
        $lib = new Storage\Semaphore($storage, $semaphore);

        $this->expectException(StorageException::class);
        $lib->exists();
    }

    /**
     * @throws StorageException
     * @throws SemaphoreException
     */
    public function testSemaphoreFailSet(): void
    {
        $semaphore = new SemaphoreStorage($this->getStorage(new \MockStorage()), 'dummy');
        $lib = new Storage\Semaphore($this->getStorage(new \MockStorage()), $semaphore);

        $this->assertTrue($lib->set(static::TESTING_CONTENT));
        $this->assertTrue($lib->exists());
        $this->assertEquals(static::TESTING_CONTENT, $lib->get());

        // semaphore action
        $semaphore->want();
        $semaphore->setStorage($this->getStorage(new \MockKillingStorage()));
        $this->expectException(StorageException::class);
        $lib->set(static::TESTING_CONTENT . static::TESTING_CONTENT);
    }

    /**
     * @throws StorageException
     */
    public function testDual(): void
    {
        $storage = $this->getStorage(new \MockStorage());
        $lib = new Storage\Dual($storage, $storage);
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
     * @throws StorageException
     */
    public function testDualNotSet(): void
    {
        $lib = new Storage\Dual($this->getStorage(new \MockFailedStorage()));
        $lib->init('');

        $this->assertFalse($lib->exists());
        $this->assertEquals('', $lib->get());
        $this->assertFalse($lib->set(static::TESTING_CONTENT));
        $this->assertFalse($lib->exists());
    }

    protected function getStorage(IStorage $mockStorage): XStorage\Storage
    {
        XStorage\Key\DirKey::setDir(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR);
        $storage = new XStorage\Factory(new XStorage\Target\Factory(), new XStorage\Format\Factory(), new XStorage\Key\Factory());
        return $storage->getStorage($mockStorage);
    }
}


class SemaphoreStorage extends Semaphore\Storage
{
    public function setStorage(XStorage\Storage $storage): void
    {
        $this->storage = $storage;
    }
}
