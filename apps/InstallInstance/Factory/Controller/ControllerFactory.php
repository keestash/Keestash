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

namespace KSA\InstallInstance\Factory\Controller;

use Keestash\Core\Service\Instance\InstallerService;
use Keestash\Legacy\Legacy;
use KSA\InstallInstance\Controller\Controller;
use KSP\Core\ILogger\ILogger;
use KSP\Core\Service\Controller\IAppRenderer;
use KSP\L10N\IL10N;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class ControllerFactory {

    public function __invoke(ContainerInterface $container): Controller {
        return new Controller(
            $container->get(IL10N::class)
            , $container->get(InstallerService::class)
            , $container->get(ILogger::class)
            , $container->get(IAppRenderer::class)
            , $container->get(TemplateRendererInterface::class)
            , $container->get(Legacy::class)
        );
    }

}