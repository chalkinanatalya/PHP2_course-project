<?php

$pdo = new PDO('sqlite:blog.sqlite');

$pdo->exec('CREATE TABLE comment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, post_id INTEGER NOT NULL, author_id INTEGER NOT NULL, text STRING)');
$pdo->exec('CREATE TABLE post (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, title STRING, text STRING)');
$pdo->exec('CREATE TABLE user (id INTEGER NOT NULL UNIQUE PRIMARY KEY AUTOINCREMENT, first_name STRING, last_name STRING, email STRING NOT NULL UNIQUE)');

//добавила мейл