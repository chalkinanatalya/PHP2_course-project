<?php
use Project\Container\DIContainer;
use Project\Repositories\Post\PostRepositoryInterface;
use Project\Repositories\Post\PostRepository;
use Project\Repositories\User\UserRepository;
use Project\Repositories\User\UserRepositoryInterface;
use Project\Repositories\Comment\CommentRepository;
use Project\Repositories\Comment\CommentRepositoryInterface;
use Project\Repositories\Like\LikeRepository;
use Project\Repositories\Like\LikeRepositoryInterface;
use Project\Connection\ConnectorInterface;
use Project\Connection\DataBaseConnector;

require_once __DIR__ . '/vendor/autoload.php';

$container = new DIContainer();

$container->bind(
    PDO::class,
    new PDO(databaseConfig()['sqlite']['DATABASE_URL'])
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

return $container;