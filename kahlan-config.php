<?php

use Kahlan\Filter\Filters;
use Kahlan\Reporter\Coverage;
use Kahlan\Reporter\Coverage\Driver\Xdebug;
use Kahlan\Reporter\Coverage\Driver\Phpdbg;

$commandLine = $this->commandLine();
$commandLine->option('coverage', 'default', 3);

Filters::apply($this, 'coverage', function($next) {
    if (!extension_loaded('xdebug') && PHP_SAPI !== 'phpdbg') {
        return;
    }
    $reporters = $this->reporters();
    $coverage = new Coverage([
        'verbosity' => $this->commandLine()->get('coverage'),
        'driver'    => PHP_SAPI !== 'phpdbg' ? new Xdebug() : new Phpdbg(),
        'path'      => $this->commandLine()->get('src'),
        'exclude'   => [
            'src/Classes/Database.php'
        ],
        'colors'    => !$this->commandLine()->get('no-colors')
    ]);
    $reporters->add('coverage', $coverage);
});
