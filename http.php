<?php
use Project\Http\Actions\Post\FindByIdAction;
use Project\Http\Actions\Post\CreatePostAction;
use Project\Http\Actions\Like\FindLikeAction;
use Project\Http\Actions\Like\CreateLikeAction;
use Project\Http\Actions\Post\DeletePostAction;
use Project\Http\Actions\User\FindByEmailAction;
use Project\Http\Actions\Comment\CreateCommentAction;
use Project\Http\Actions\Auth\LogIn;
use Project\Http\Response\ErrorResponse;
use Project\Exceptions\HttpException;
use Project\Http\Request\Request;
use Psr\Log\LoggerInterface;

$container = require __DIR__ . '/bootstrap.php';

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);

$logger = $container->get(LoggerInterface::class);

try {
    $path = $request->path();
} catch (HttpException) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}

$routes = [
    'GET' => [
        '/user/show' => FindByEmailAction::class,
        '/post/show' => FindByIdAction::class,
        '/like/show' => FindLikeAction::class,
    ],
    'POST' => [
        '/login' => LogIn::class,
        '/post/create' => CreatePostAction::class,
        '/post/comment' => CreateCommentAction::class,
        '/post/like' => CreateLikeAction::class,
    ],
    'DELETE' => [
        '/post' => DeletePostAction::class,
    ]
];

if (!array_key_exists($method, $routes)) {
    $logger->notice($message);
    (new ErrorResponse('Method not found'))->send();
    return;
}

if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Path not found'))->send();
    return;
}

$actionClassName = $routes[$method][$path];
$action = $container->get($actionClassName);

try {
    $response = $action->handle($request);
} catch (Exception $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse($e->getMessage()))->send();
    return;
}

$response->send();