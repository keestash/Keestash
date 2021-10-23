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

namespace KSA\PasswordManager\Api\Node\Organization;

use DateTime;
use Exception;
use KSA\PasswordManager\Repository\Node\NodeRepository;
use KSA\PasswordManager\Repository\Node\OrganizationRepository as OrganizationNodeRepository;
use KSA\Settings\Repository\IOrganizationRepository;
use KSP\Api\IResponse;
use KSP\Core\ILogger\ILogger;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Add implements RequestHandlerInterface {

    private OrganizationNodeRepository $organizationNodeRepository;
    private IOrganizationRepository    $organizationRepository;
    private NodeRepository             $nodeRepository;
    private ILogger                    $logger;

    public function __construct(
        OrganizationNodeRepository $organizationNodeRepository
        , IOrganizationRepository  $organizationRepository
        , NodeRepository           $nodeRepository
        , ILogger                  $logger
    ) {
        $this->organizationNodeRepository = $organizationNodeRepository;
        $this->organizationRepository     = $organizationRepository;
        $this->nodeRepository             = $nodeRepository;
        $this->logger                     = $logger;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface {
        $parameters = json_decode((string) $request->getBody(), true);

        $nodeId         = (int) ($parameters['node_id'] ?? 0);
        $organizationId = (int) ($parameters['organization_id'] ?? 0);

        if ($nodeId === 0 || $organizationId === 0) {
            return new JsonResponse(
                'node id or organization id not given'
                , IResponse::NOT_ACCEPTABLE
            );
        }

        $node = $this->nodeRepository->getNode($nodeId, 0, 0);

        if (null !== $node->getOrganization()) {
            return new JsonResponse(
                'node still belongs to an organization'
                , IResponse::FORBIDDEN
            );
        }

        $organization = $this->organizationRepository->get($organizationId);

        if (null === $organization) {
            return new JsonResponse(
                'no organization found'
                , IResponse::NOT_FOUND
            );
        }

        if (null === $organization->getActiveTs() || $organization->getActiveTs() < (new DateTime())) {
            return new JsonResponse(
                'node is not active'
                , IResponse::FORBIDDEN
            );
        }

        try {
            $this->organizationNodeRepository->addNodeToOrganization(
                $node
                , $organization
            );
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage() . ': ' . $exception->getTraceAsString());
            return new JsonResponse(
                'could not add node to organization'
                , IResponse::INTERNAL_SERVER_ERROR
            );
        }

        return new JsonResponse(
            ['organization' => $organization]
        );
    }

}