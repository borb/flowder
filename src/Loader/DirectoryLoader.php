<?php

namespace Imjoehaines\Flowder\Loader;

class DirectoryLoader implements LoaderInterface
{
    public function load($directory)
    {
        $phpFiles = glob(rtrim($directory, '/') . '/*.php');

        $fileLoader = new FileLoader();

        return array_merge(...array_map([$fileLoader, 'load'], $phpFiles));
    }
}
