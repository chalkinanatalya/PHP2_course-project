<?php

namespace Project\Connection;

use PDO;

interface ConnectorInterface
{
    public static function getConnection(): PDO;
}