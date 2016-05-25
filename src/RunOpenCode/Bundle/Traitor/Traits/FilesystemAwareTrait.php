<?php

namespace RunOpenCode\Bundle\Traitor\Traits;

use Symfony\Component\Filesystem\Filesystem;

class FilesystemAwareTrait
{
    protected $filesystem;

    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }
}
