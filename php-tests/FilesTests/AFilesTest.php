<?php

namespace FilesTests;


use kalanis\kw_files\Access;
use kalanis\kw_files\FilesException;
use kalanis\kw_paths\PathsException;
use kalanis\kw_storage\Interfaces\ITarget;
use kalanis\kw_storage\Storage as XStorage;
use kalanis\kw_storage\StorageException;


abstract class AFilesTest extends \CommonTestClass
{
    /**
     * @param ITarget|string $mockStorage
     * @throws FilesException
     * @throws PathsException
     * @throws StorageException
     * @return Access\CompositeAdapter
     */
    protected function getStorage($mockStorage): Access\CompositeAdapter
    {
        return (new Access\Factory())->getClass(
            (new XStorage\Factory(new XStorage\Key\Factory(), new XStorage\Target\Factory()))->getStorage($mockStorage)
        );
    }
}
