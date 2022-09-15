<?php

namespace Project\Connection;

use PDO;

class DataBaseConnector implements ConnectorInterface
{
    public static function getConnection(): PDO
    {
        return new PDO(databaseConfig()['sqlite']['DATABASE_URL']);
    }
}