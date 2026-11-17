<?php

namespace Omatech\Editora\Admin\Util;

/**
 * Doctrine DBAL parameters for the admin's own connection (class listing, export and import), from the application's MySQL
 * connection. When that connection carries the editors' offset (the connector sets it from EDITORA_DATABASE_TIMEZONE, since
 * Laravel Cloud's MySQL runs in UTC and publishing dates are stored as editors type them), this session opens in it too.
 */
final class DoctrineParams
{
    public static function fromLaravel(array $conn): array
    {
        $params = [
            'dbname' => $conn['database'],
            'user' => $conn['username'],
            'password' => $conn['password'] ?? '',
            'host' => $conn['host'],
            'driver' => 'pdo_mysql',
            'charset' => 'utf8',
        ];
        $offset = $conn['timezone'] ?? null;
        if (is_string($offset)) {
            if (preg_match('/^[+-](?:0\d|1[0-4]):[0-5]\d$/D', $offset) !== 1) {
                throw new \InvalidArgumentException('The database time zone must be an offset such as +01:00');
            }
            // Pdo\Mysql (PHP 8.4+) replaces the PDO::MYSQL_* constants that PHP 8.5 deprecates.
            $initCommand = \defined('Pdo\Mysql::ATTR_INIT_COMMAND') ? \Pdo\Mysql::ATTR_INIT_COMMAND : \PDO::MYSQL_ATTR_INIT_COMMAND;
            $params['driverOptions'] = [$initCommand => "SET time_zone = '".$offset."'"];
        }

        return $params;
    }
}
