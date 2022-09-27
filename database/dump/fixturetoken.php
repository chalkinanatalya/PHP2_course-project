<?php

$pdo = new PDO('sqlite:blog.sqlite');

$pdo->exec('ALTER TABLE tokens RENAME COLUMN author_id TO email');