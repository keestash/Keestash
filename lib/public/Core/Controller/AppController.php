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

namespace KSP\Core\Controller;

use Keestash\View\Navigation\App\NavigationList;
use KSP\Core\Service\Controller\IAppRenderer;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class AppController implements IAppController, RequestHandlerInterface {

    private IAppRenderer $appRenderer;

    private NavigationList $navigationList;

    public function __construct(IAppRenderer $appRenderer) {
        $this->appRenderer    = $appRenderer;
        $this->navigationList = new NavigationList();
    }

    public abstract function run(ServerRequestInterface $request): string;


    protected function setAppNavigation(NavigationList $navigationList): void {
        $this->navigationList = $navigationList;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface {
        $hasAppNavigation =
            (static::class === AppController::class)
            && $this->navigationList->length() > 0;
        $static = $this instanceof StaticAppController
        || $this instanceof  ContextLessAppController;
        return new HtmlResponse(
            $this->appRenderer->render(
                $request
                , $hasAppNavigation
                , $this->run($request)
                , $static
                , $this->navigationList
            )
        );
    }

}
