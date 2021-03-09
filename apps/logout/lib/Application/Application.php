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

namespace KSA\Logout\Application;

use Keestash;
use KSA\Logout\Controller\Logout;
use KSP\Core\Manager\RouterManager\IRouterManager;

class Application extends Keestash\App\Application {

    public const APP_ID            = "logout";
    public const PERMISSION_LOGOUT = "logout";
    public const LOGOUT            = "logout";

    public function register(): void {

        $this->registerRoute(
            Application::LOGOUT
            , Logout::class
            , [IRouterManager::GET]
        );

        $this->registerPublicRoute(Application::LOGOUT);

        $this->addSetting(
            self::LOGOUT
            , Keestash::getServer()
            ->getL10N()
            ->translate("Logout")
            , "fas fa-sign-out-alt"
            , 5
        );

        $this->registerJavascript();
    }

    private function registerJavascript(): void {
        $this->addJavaScriptFor(
            Application::APP_ID
            , "logout"
            , "logout"
        );

    }

}