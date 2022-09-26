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

use Keestash\ConfigProvider as CoreConfigProvider;
use KSA\Apps\Api\GetApps;
use KSA\Apps\Api\UpdateApp;
use KSA\Apps\ConfigProvider;
use KSP\Api\IRoute;
use KSP\Api\IVerb;

return [
    CoreConfigProvider::ROUTES => [
        [
            IRoute::PATH         => ConfigProvider::ROUTE_NAME_APPS_UPDATE
            , IRoute::MIDDLEWARE => UpdateApp::class
            , IRoute::METHOD     => IVerb::POST
            , IRoute::NAME       => UpdateApp::class
        ],
        [
            IRoute::PATH         => ConfigProvider::ROUTE_NAME_APPS_GET_ALL
            , IRoute::MIDDLEWARE => GetApps::class
            , IRoute::METHOD     => IVerb::GET
            , IRoute::NAME       => GetApps::class
        ]

    ]
];