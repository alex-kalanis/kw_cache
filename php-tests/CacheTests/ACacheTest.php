<?php

namespace CacheTests;


use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Interfaces\ICache;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\Interfaces\ITarget;
use kalanis\kw_storage\Storage as XStorage;


abstract class ACacheTest extends \CommonTestClass
{
    protected function getStorage(ITarget $mockStorage): IStorage
    {
        XStorage\Key\DirKey::setDir(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR);
        $storage = new XStorage\Factory(new XStorage\Key\Factory(), new XStorage\Target\Factory());
        return $storage->getStorage($mockStorage);
    }
}


class MockCacheFail implements ICache
{
    public function init(array $what): void
    {
    }

    public function exists(): bool
    {
        return false;
    }

    public function set(string $content): bool
    {
        return false;
    }

    public function get(): string
    {
        return '';
    }

    public function clear(): void
    {
    }
}


class MockCacheKill implements ICache
{
    public function init(array $what): void
    {
    }

    public function exists(): bool
    {
        return true;
    }

    public function set(string $content): bool
    {
        return true;
    }

    public function get(): string
    {
        return '';
    }

    public function clear(): void
    {
        throw new CacheException('mock test');
    }
}
