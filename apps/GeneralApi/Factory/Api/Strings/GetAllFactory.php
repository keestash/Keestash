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

namespace KSA\GeneralApi\Factory\Api\Strings;

use Keestash\Core\Manager\StringManager\FrontendManager;
use KSA\GeneralApi\Api\Strings\GetAll;
use KSP\Core\Cache\ICacheService;
use Psr\Container\ContainerInterface;

class GetAllFactory {

    public function __invoke(ContainerInterface $container): GetAll {
        return new GetAll(
            $container->get(FrontendManager::class)
            , $container->get(ICacheService::class)
        );
    }

}