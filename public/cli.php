<?php
use Project\Repositories\User\UserRepository;
use Project\Repositories\Post\PostRepository;
use Project\Repositories\Comment\CommentRepository;

use Project\Argument\Argument;
use Project\Commands\UserCommand\CreateUserCommand;
use Project\Exceptions\CommandException;
use Project\Commands\BlogCommand\CreateBlogCommand;
use Project\Commands\CommentCommand\CreateCommentCommand;

require_once __DIR__ . '/autoload_runtime.php';

$faker = Faker\Factory::create();
$userRepository = new UserRepository();
$postRepository = new PostRepository();
$commentRepository = new CommentRepository();


try {
    if($argv[1] === 'user') 
    {
        $userCommand = new CreateUserCommand ($userRepository);
        $userCommand->handle(Argument::fromArgv($argv));
        echo 'Data is successfully written';
    }

    if($argv[1] === 'post') 
    {
        $postCommand = new CreateBlogCommand ($postRepository);
        $postCommand->handle(Argument::fromArgv($argv));
        echo 'Data is successfully written';
    }

    if($argv[1] === 'comment') 
    {
        $commentCommand = new CreateCommentCommand($commentRepository);
        $commentCommand->handle(Argument::fromArgv($argv));
        echo 'Data is successfully written';
    }

    
} catch (CommandException)
{
    echo $commandException->getMessage();
}