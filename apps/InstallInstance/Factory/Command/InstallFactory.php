<?php
declare(strict_types=1);
/**
 * Keestash
 *
 * Copyright (C) <2022> <Dogan Ucar>
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

namespace KSA\InstallInstance\Factory\Command;

use Keestash\Core\Service\File\FileService;
use Keestash\Core\Service\Instance\InstallerService;
use Keestash\Core\System\Installation\Instance\LockHandler;
use KSA\InstallInstance\Command\Install;
use KSP\Core\Repository\File\IFileRepository;
use Psr\Log\LoggerInterface;
use KSP\Core\Service\User\IUserService;
use KSP\Core\Service\User\Repository\IUserRepositoryService;
use Laminas\Config\Config;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class InstallFactory implements FactoryInterface {

    public function __invoke(
        ContainerInterface $container
        ,                  $requestedName
        , ?array           $options = null
    ): Install {
        return new Install(
            $container->get(Config::class)
            , $container->get(LoggerInterface::class)
            , $container->get(InstallerService::class)
            , $container->get(LockHandler::class)
            , $container->get(IUserRepositoryService::class)
            , $container->get(IUserService::class)
            , $container->get(IFileRepository::class)
            , $container->get(FileService::class)
        );
    }

}