<?php

namespace app\database;

use PDO;

class Connection
{
    private static ?PDO $connect = null;

    public static function getConnection()
    {
        if (!self::$connect) {
            self::$connect = new PDO('mysql:host=localhost;dbname=blog_ci', 'root', '', [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            ]);
        }

        return self::$connect;
    }
}
