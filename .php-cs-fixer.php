<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfig;

$finder = (new Finder())
    ->in(__DIR__);

return (new Config())
    ->setParallelConfig(new ParallelConfig())
    ->setFinder($finder);
