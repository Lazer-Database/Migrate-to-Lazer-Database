<?php

declare(strict_types=1);

namespace Lazer\Migrate\Test\VfsHelper;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

trait Config {

    /**
     * @var vfsStreamDirectory
     */
    protected $root;

    protected function setUpFilesystem()
    {
        $this->root = vfsStream::setup('data');
        vfsStream::copyFromFileSystem(ROOT . 'tests/db');
    }
}
