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

namespace KSA\GeneralApi\Factory\Api\Demo;

use Keestash\Core\Service\User\UserService;
use KSA\GeneralApi\Api\Demo\AddEmailAddress;
use KSA\GeneralApi\Repository\DemoUsersRepository;
use KSP\Core\Manager\CookieManager\ICookieManager;
use Psr\Container\ContainerInterface;

class AddEmailAddressFactory {

    public function __invoke(ContainerInterface $container): AddEmailAddress {
        return new AddEmailAddress(
            $container->get(DemoUsersRepository::class)
            , $container->get(UserService::class)
            , $container->get(ICookieManager::class)
        );
    }

}