<?php

namespace StorageTests;


use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\Storage as XStorage;


abstract class AStorageTest extends \CommonTestClass
{
    /**
     * @param IStorage|string $mockStorage
     * @return XStorage\Storage
     */
    protected function getStorage($mockStorage): XStorage\Storage
    {
        XStorage\Key\DirKey::setDir(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR);
        $storage = new XStorage\Factory(new XStorage\Key\Factory(), new XStorage\Target\Factory());
        return $storage->getStorage($mockStorage);
    }
}
