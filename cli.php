<?php
require_once __DIR__ . '\vendor\autoload.php';

use ChalkinaNatalia\Project\User\User;
use ChalkinaNatalia\Project\Blog\Post;
use ChalkinaNatalia\Project\Comment\Comment;

$faker = Faker\Factory::create();

if($argv[1] === 'user') 
{
    $user = new User(1, $faker->firstName, $faker->lastName);
    print $user;
}

if($argv[1] === 'post') 
{
    $post = new Post(2, 2, $faker->realText($maxNbChars = 10, $indexSize = 2), $faker->realTextBetween($minNbChars = 50, $maxNbChars = 100, $indexSize = 2));
    print $post;
}

if($argv[1] === 'comment') 
{
    $comment = new Comment(2, 2, 3, $faker->realTextBetween($minNbChars = 50, $maxNbChars = 200, $indexSize = 2));
    print $comment;
}