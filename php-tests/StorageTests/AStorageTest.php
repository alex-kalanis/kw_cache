<?php

namespace StorageTests;


use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\Interfaces\ITarget;
use kalanis\kw_storage\Storage as XStorage;
use kalanis\kw_storage\StorageException;


abstract class AStorageTest extends \CommonTestClass
{
    /**
     * @param ITarget|string $mockStorage
     * @throws StorageException
     * @return IStorage
     */
    protected function getStorage($mockStorage): IStorage
    {
        XStorage\Key\StaticPrefixKey::setPrefix(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR);
        $storage = new XStorage\Factory(new XStorage\Key\Factory(), new XStorage\Target\Factory());
        return $storage->getStorage($mockStorage);
    }
}
