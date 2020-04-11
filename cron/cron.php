#!/usr/bin/env php
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

use Keestash\Core\Service\Config\ConfigService;

(function () {

    chdir(dirname(__DIR__));

    require_once __DIR__ . '/../lib/versioncheck.php';
    require_once __DIR__ . '/../lib/filecheck.php';
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../lib/Keestash.php';
    Keestash::init();

    $backgrounder  = Keestash::getServer()->getBackgrounder();
    $jobRepository = Keestash::getServer()->getJobRepository();
    /** @var ConfigService $configService */
    $configService = Keestash::getServer()->query(ConfigService::class);

    $backgrounder->setDebug(
        (bool) $configService->getValue("debug", false)
    );

    $jobRepository->updateJobs($backgrounder->run());
})();