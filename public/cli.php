<?php
use Project\Repositories\User\UserRepository;
use Project\Repositories\Post\PostRepository;
use Project\Repositories\Comment\CommentRepository;

use Project\User\User;
use Project\Blog\Post;
use Project\Comment\Comment;

require_once __DIR__ . '/autoload_runtime.php';

$faker = Faker\Factory::create();
$userRepository = new UserRepository();
$postRepository = new PostRepository();
$commentRepository = new CommentRepository();

if($argv[1] === 'user') 
{
    $user = new User($faker->firstName, $faker->lastName);
    $userRepository->save($user);

    print $user;
}

if($argv[1] === 'post') 
{
    $user = $userRepository->get(1);
    $post = new Post($user->getId(), $faker->realText($maxNbChars = 10, $indexSize = 2), $faker->realTextBetween($minNbChars = 50, $maxNbChars = 100, $indexSize = 2));
    $postRepository->save($post);
    
    print $post;
}

if($argv[1] === 'comment') 
{
    $user = $userRepository->get(1);
    $post = $postRepository->get(2);
    $comment = new Comment($post->getId(), $user->getId(), $faker->realTextBetween($minNbChars = 50, $maxNbChars = 200, $indexSize = 2));
    $commentRepository->save($comment);

    print $comment;
}