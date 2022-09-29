<?php

use Project\Container\DIContainer;

use Project\Repositories\Post\PostRepository;
use Project\Repositories\Post\PostRepositoryInterface;

use Project\Repositories\User\UserRepository;
use Project\Repositories\User\UserRepositoryInterface;

use Project\Repositories\Comment\CommentRepository;
use Project\Repositories\Comment\CommentRepositoryInterface;

use Project\Repositories\Like\LikeRepository;
use Project\Repositories\Like\LikeRepositoryInterface;

use Project\Connection\ConnectorInterface;
use Project\Connection\DataBaseConnector;

use Project\Http\Auth\PasswordAuthenticationInterface;
use Project\Http\Auth\PasswordAuthentication;

use Project\Http\Auth\TokenAuthenticationInterface;
use Project\Http\Auth\BearerTokenAuthentication;

use Project\Repositories\AuthToken\AuthTokenRepositoryInterface;
use Project\Repositories\AuthToken\AuthTokenRepository;

use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require_once __DIR__ . '/vendor/autoload.php';
\Dotenv\Dotenv::createImmutable(__DIR__)->safeLoad();


$container = new DIContainer();

$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/' . $_SERVER['SQLITE_DB_PATH'])
);

$container->bind(
    ConnectorInterface::class,
    DataBaseConnector::class
);

$container->bind(
    PostRepositoryInterface::class,
    PostRepository::class
);

$container->bind(
    UserRepositoryInterface::class,
    UserRepository::class
);

$container->bind(
    CommentRepositoryInterface::class,
    CommentRepository::class
);

$container->bind(
    LikeRepositoryInterface::class,
    LikeRepository::class
);

$container->bind(
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
);

$container->bind(
    AuthTokenRepositoryInterface::class,
    AuthTokenRepository::class
);

$container->bind(
    TokenAuthenticationInterface::class,
    BearerTokenAuthentication::class
);

$logger = (new Logger('blog'));

if ('yes' === $_SERVER['LOG_TO_FILES']) {
    $logger
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.log'
        ))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.error.log',
            level: Logger::ERROR,
            bubble: false,
        ));
}

if ('yes' === $_SERVER['LOG_TO_CONSOLE']) {
    $logger
        ->pushHandler(
            new StreamHandler("php://stdout")
        );
}    

$container->bind(
    LoggerInterface::class,
    $logger
);

return $container;
