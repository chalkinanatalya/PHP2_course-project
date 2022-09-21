<?php

use Project\Argument\Argument;
use Project\Commands\UserCommand\CreateUserCommand;
use Project\Exceptions\CommandException;
use Project\Commands\PostCommand\CreatePostCommand;
use Project\Commands\CommentCommand\CreateCommentCommand;

require_once __DIR__ . '/autoload_runtime.php';
$container = require __DIR__ . '/../bootstrap.php';

$command = $container->get(CreateUserCommand::class);

try {
    $command->handle(Argument::fromArgv($argv));
} catch (Exception $e) {
    echo "{$e->getMessage()}\n";
}    

// try {
//     if($argv[1] === 'user') 
//     {
//         $userCommand = new CreateUserCommand ($userRepository);
//         $userCommand->handle(Argument::fromArgv($argv));
//         echo 'Data is successfully written';
//     }

//     if($argv[1] === 'post') 
//     {
//         $postCommand = new CreatePostCommand ($postRepository);
//         $postCommand->handle(Argument::fromArgv($argv));
//         echo 'Data is successfully written';
//     }

//     if($argv[1] === 'comment') 
//     {
//         $commentCommand = new CreateCommentCommand($commentRepository);
//         $commentCommand->handle(Argument::fromArgv($argv));
//         echo 'Data is successfully written';
//     }

    
// } catch (CommandException $commandException)
// {
//     echo $commandException->getMessage();
// }