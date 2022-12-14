<?php

$pdo = new PDO('sqlite:blog.sqlite');

$pdo->exec('CREATE TABLE comment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, post_id INTEGER NOT NULL, author_id INTEGER NOT NULL, text STRING)');
$pdo->exec('CREATE TABLE post (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, title STRING, text STRING)');
$pdo->exec('CREATE TABLE user (id INTEGER NOT NULL UNIQUE PRIMARY KEY AUTOINCREMENT, first_name STRING, last_name STRING, email STRING NOT NULL UNIQUE, password STRING NOT NULL)');
$pdo->exec('CREATE TABLE like (id INTEGER NOT NULL UNIQUE PRIMARY KEY AUTOINCREMENT, post_id INTEGER NOT NULL, author_id INTEGER NOT NULL)');
$pdo->exec('CREATE TABLE tokens (token TEXT NOT NULL CONSTRAINT token_primary_key PRIMARY KEY, email TEXT NOT NULL, expires_on TEXT NOT NULL)');