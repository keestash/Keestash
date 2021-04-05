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

namespace Keestash\Middleware;

use Keestash\Core\Service\Router\Verification;
use KSP\App\IApp;
use KSP\Core\DTO\Token\IToken;
use KSP\Core\Service\Core\Environment\IEnvironmentService;
use Laminas\Config\Config;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Router\Route;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class KeestashHeaderMiddleware implements MiddlewareInterface {

    private RouterInterface     $router;
    private Config              $config;
    private Verification        $verification;
    private IEnvironmentService $environmentService;

    public function __construct(
        RouterInterface $router
        , Config $config
        , Verification $verification
        , IEnvironmentService $environmentService
    ) {
        $this->router             = $router;
        $this->config             = $config;
        $this->verification       = $verification;
        $this->environmentService = $environmentService;
    }

    private function getMatchedPath(ServerRequestInterface $request): string {
        $matchedRoute = $this->router->match($request)->getMatchedRoute();

        if ($matchedRoute instanceof Route) {
            return $this->router->match($request)->getMatchedRoute()->getPath();
        }
        return '';
    }

    private function getPublicRoutes(): array {
        if (true === $this->environmentService->isWeb()) {
            return $publicRoutes = $this->config
                ->get(IApp::CONFIG_PROVIDER_WEB_ROUTER)
                ->get(IApp::CONFIG_PROVIDER_PUBLIC_ROUTES)
                ->toArray();
        }

        if (true === $this->environmentService->isApi()) {
            return $publicRoutes = $this->config
                ->get(IApp::CONFIG_PROVIDER_API_ROUTER)
                ->get(IApp::CONFIG_PROVIDER_PUBLIC_ROUTES)
                ->toArray();
        }
        return [];
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        if (true === $this->environmentService->isWeb()) {
            return $handler->handle($request);
        }

        $publicRoutes = $this->getPublicRoutes();
        $currentPath  = $this->getMatchedPath($request);

        foreach ($publicRoutes as $publicRoute) {
            if ($currentPath === $publicRoute) {
                return $handler->handle($request);
            }
        }

        $token = $this->verification->verifyToken(
            array_merge(
                $request->getHeaders()
                , $request->getParsedBody()
                , $request->getQueryParams()
            )
        );

        if (null === $token) {
            return new JsonResponse(['session expired'], 401);
        }

        return $handler->handle($request->withAttribute(IToken::class, $token));
    }

}
