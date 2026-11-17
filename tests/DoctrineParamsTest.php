<?php

namespace Omatech\Editora\Tests;

use Omatech\Editora\Admin\Util\DoctrineParams;

/**
 * The class listing, export and import open their own Doctrine connection. When the application's MySQL connection carries
 * the editors' offset (the connector sets it from EDITORA_DATABASE_TIMEZONE, since Laravel Cloud's MySQL runs in UTC), that
 * session opens in the same zone, so the dates it reads and writes agree with the rest of the admin.
 */
final class DoctrineParamsTest extends TestCase
{
    private const CONNECTION = ['driver' => 'mysql', 'host' => 'db', 'database' => 'editora', 'username' => 'editor', 'password' => 'fixture-only'];

    private static function initCommand(): int
    {
        return \defined('Pdo\Mysql::ATTR_INIT_COMMAND') ? \Pdo\Mysql::ATTR_INIT_COMMAND : \PDO::MYSQL_ATTR_INIT_COMMAND;
    }

    public function test_the_session_opens_in_the_offset_of_the_application_connection(): void
    {
        $params = DoctrineParams::fromLaravel(self::CONNECTION + ['timezone' => '+01:00']);

        self::assertSame("SET time_zone = '+01:00'", $params['driverOptions'][self::initCommand()]);
        self::assertSame(['dbname' => 'editora', 'user' => 'editor', 'password' => 'fixture-only', 'host' => 'db', 'driver' => 'pdo_mysql', 'charset' => 'utf8'],
            array_diff_key($params, ['driverOptions' => true]));
    }

    public function test_without_an_offset_the_session_keeps_the_server_zone(): void
    {
        self::assertArrayNotHasKey('driverOptions', DoctrineParams::fromLaravel(self::CONNECTION));
    }

    public function test_only_an_offset_reaches_the_statement(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        DoctrineParams::fromLaravel(self::CONNECTION + ['timezone' => "+01:00'; DROP TABLE omp_instances; --"]);
    }
}
