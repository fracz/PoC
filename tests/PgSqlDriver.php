<?php
/**
 * Created by PhpStorm.
 * User: szymon
 * Date: 18.09.16
 * Time: 08:27
 */

namespace Tests;

use Doctrine\DBAL\Driver\PDOPgSql\Driver;

class PgSqlDriver extends Driver
{
    private static $connection;

    public function connect(array $params, $username = null, $password = null, array $driverOptions = array())
    {
        if (null === self::$connection) {
            self::$connection = parent::connect($params, $username, $password, $driverOptions);
        }

        return self::$connection;
    }
}