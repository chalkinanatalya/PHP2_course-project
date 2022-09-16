<?php
use Project\Http\Actions\Post\FindByIdAction;
use Project\Http\Actions\Post\CreatePostAction;
use Project\Http\Actions\Post\DeletePostAction;
use Project\Http\Actions\User\FindByEmailAction;
use Project\Http\Actions\Comment\CreateCommentAction;
use Project\Http\Response\ErrorResponse;
use Project\Exceptions\HttpException;
use Project\Http\Request\Request;
use Project\Repositories\Comment\CommentRepository;
use Project\Repositories\Post\PostRepository;
use Project\Repositories\User\UserRepository;

require_once __DIR__ . '/vendor/autoload.php';

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);

$conten = file_get_contents('php://input');

try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

$routes = [
    'GET' => [
        '/user/show' => new FindByEmailAction(new UserRepository()),
        '/post/show' => new FindByIdAction(new PostRepository()),
    ],
    'POST' => [
        '/post/create' => new CreatePostAction(
            new PostRepository(),
            new UserRepository()
        ),
        '/post/comment' => new CreateCommentAction(
            new CommentRepository(),
            new PostRepository(),
            new UserRepository()
        ),
    ],
    'DELETE' => [
        '/post' => new DeletePostAction(
            new PostRepository()
        ),
    ]
];

if (!array_key_exists($method, $routes)) {
    (new ErrorResponse('Not found'))->send();
    return;
}

if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Not found'))->send();
    return;
}

$action = $routes[$method][$path];
try {
    $response = $action->handle($request);
} catch (Exception $e) {
    (new ErrorResponse($e->getMessage()))->send();
}

$response->send();