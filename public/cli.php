<?php

use Symfony\Component\Console\Application;
use Project\Console\User\CreateUser;
use Project\Console\User\UpdateUser;
use Project\Console\Post\DeletePost;
use Project\Console\Data\PopulateDB;


require_once __DIR__ . '/autoload_runtime.php';
$container = require __DIR__ . '/../bootstrap.php';

$application = new Application();
$commandsClasses = [
    CreateUser::class,
    DeletePost::class,
    UpdateUser::class,
    PopulateDB::class,
];

foreach ($commandsClasses as $commandClass) {
    $command = $container->get($commandClass);
    $application->add($command);
}
$application->run();
