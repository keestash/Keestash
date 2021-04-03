<?php
declare(strict_types=1);
/**
 * Keestash
 *
 * Copyright (C) <2021> <Dogan Ucar>
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

namespace Keestash\Factory\Core\Service\HTTP;

use Keestash\Core\Service\HTTP\PersistenceService;
use KSP\Core\ILogger\ILogger;
use KSP\Core\Manager\CookieManager\ICookieManager;
use KSP\Core\Manager\SessionManager\ISessionManager;
use KSP\Core\Service\HTTP\IPersistenceService;
use Psr\Container\ContainerInterface;

class PersistenceServiceFactory {

    public function __invoke(ContainerInterface $container): IPersistenceService {
        return new PersistenceService(
            $container->get(ISessionManager::class)
            , $container->get(ICookieManager::class)
            , $container->get(ILogger::class)
        );
    }

}