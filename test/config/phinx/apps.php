<?php
declare(strict_types=1);

/**
 * Keestash
 *
 * Copyright (C) <2019> <Dogan Ucar>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

use Doctrine\DBAL\Connection;
use Keestash\ConfigProvider;
use Keestash\Core\Repository\Migration\Base\KeestashMigration;
use Laminas\Config\Config;
use Psr\Container\ContainerInterface;

/** @noinspection PhpIncludeInspection */
require realpath(__DIR__ . '/../../../config/config.php');

/** @var ContainerInterface $container */
$container = require __DIR__ . '/../service_manager.php';

/** @var Config $config */
$config  = $container->get(Config::class);
$dirname = realpath($config->get(ConfigProvider::INSTANCE_PATH));

/** @var Connection $connection */
$connection = $container->get(Connection::class);
$pdo        = $connection
    ->getWrappedConnection()
    ->getWrappedConnection()
;

return [
    'environments'           => [
        'default_environment' => 'development'
        , 'development'       => [
            'name'         => $CONFIG['db_name']
            , 'connection' => $pdo
            , 'adapter'    => 'sqlite'
            , "suffix"     => "sqlite"
        ]
        , 'production'        => [
            'name'         => $CONFIG['db_name']
            , 'connection' => $pdo
            , 'adapter'    => 'sqlite'
            , "suffix"     => "sqlite"
        ]
        , 'testing'           => [
            'name'         => $CONFIG['db_name']
            , 'connection' => $pdo
            , 'adapter'    => 'sqlite'
            , "suffix"     => "sqlite"
        ]
    ]
    , 'migration_base_class' => KeestashMigration::class
    , "paths"                => [
        "migrations" => [
            "$dirname/apps/*/Repository/Migration"
        ]
    ]
];
