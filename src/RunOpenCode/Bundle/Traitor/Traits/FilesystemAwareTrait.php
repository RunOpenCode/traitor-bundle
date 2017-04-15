<?php
/*
 * This file is part of the  TraitorBundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\Traitor\Traits;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Class FilesystemAwareTrait
 *
 * @package RunOpenCode\Bundle\Traitor\Traits
 *
 * @codeCoverageIgnore
 */
trait FilesystemAwareTrait
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }
}
