<?php

namespace BasicTests;


use kalanis\kw_cache\Cache;
use kalanis\kw_cache\Interfaces\ICache;
use kalanis\kw_cache\Simple;
use kalanis\kw_cache\Storage as CStor;
use kalanis\kw_storage\Storage;


class DualsTest extends \CommonTestClass
{
    public function testDualStorageBasic(): void
    {
        Storage\Key\DirKey::setDir(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR);
        $storage = new Storage\Factory(new Storage\Key\Factory(), new Storage\Target\Factory());

        // one target
        $lib = new XDual1($storage->getStorage(new \MockStorage()));
        $this->assertEquals($lib->getStorage(), $lib->getReload());

        // multiple targets
        $lib = new XDual1($storage->getStorage(new \MockStorage()), $storage->getStorage('volume'));
        $this->assertNotEquals($lib->getStorage(), $lib->getReload());
    }

    public function testDualCacheBasic(): void
    {
        // one target
        $lib = new XDual2(new Simple\Memory());
        $this->assertEquals($lib->getStorage(), $lib->getReload());

        // multiple targets
        $lib = new XDual2(new Simple\Memory(), new Simple\Variable());
        $this->assertNotEquals($lib->getStorage(), $lib->getReload());
    }
}


class XDual1 extends CStor\Dual
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


class XDual2 extends Cache\Dual
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
