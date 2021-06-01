<?php
declare(strict_types=1);
/**
 * Keestash
 *
 * Copyright (C) <2020> <Dogan Ucar>
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

namespace KSA\PasswordManager\Api\Node\Folder;

use DateTime;
use Keestash\Api\Response\LegacyResponse;
use KSA\PasswordManager\Application\Application;
use KSA\PasswordManager\Entity\Folder\Folder;
use KSA\PasswordManager\Entity\Node;
use KSA\PasswordManager\Repository\Node\NodeRepository;
use KSA\PasswordManager\Service\Node\NodeService;
use KSP\Api\IResponse;
use KSP\Core\DTO\Token\IToken;
use KSP\L10N\IL10N;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class NodeCreate
 *
 * @package KSA\PasswordManager\Api
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class Create implements RequestHandlerInterface {

    private IL10N          $translator;
    private NodeRepository $nodeRepository;
    private NodeService    $nodeService;

    public function __construct(
        IL10N $l10n
        , NodeRepository $nodeRepository
        , NodeService $nodeService
    ) {
        $this->translator     = $l10n;
        $this->nodeRepository = $nodeRepository;
        $this->nodeService    = $nodeService;
    }


    public function handle(ServerRequestInterface $request): ResponseInterface {
        /** @var IToken $token */
        $token      = $request->getAttribute(IToken::class);
        $parameters = json_decode((string) $request->getBody(), true);
        $title      = $parameters["title"] ?? null;
        $parent     = $parameters["parent"] ?? null;

        if (false === $this->isValid($title) || false === $this->isValid($parent)) {

            return LegacyResponse::fromData(
                IResponse::RESPONSE_CODE_NOT_OK
                , [
                    "message" => $this->translator->translate("no parameters set")
                ]
            );

        }

        $parentEdge = $this->getParentEdge($parent, $token);

        if (null === $parentEdge || $parentEdge->getUser()->getId() !== $token->getUser()->getId()) {

            return LegacyResponse::fromData(
                IResponse::RESPONSE_CODE_NOT_OK
                , [
                    "message" => $this->translator->translate("no parent found")
                ]
            );

        }

        $folder = new Folder();
        $folder->setUser($token->getUser());
        $folder->setName((string) $title);
        $folder->setType(Node::FOLDER);
        $folder->setCreateTs(new DateTime());

        $lastId = $this->nodeRepository->addFolder($folder);

        if (null === $lastId || 0 === $lastId) {
            return LegacyResponse::fromData(
                IResponse::RESPONSE_CODE_NOT_OK
                , [
                    "message" => $this->translator->translate("could not add")
                ]
            );
        }

        $folder->setId($lastId);

        $this->nodeRepository->addEdge(
            $this->nodeService->prepareRegularEdge(
                $folder
                , $parentEdge
            )
        );

        return LegacyResponse::fromData(
            IResponse::RESPONSE_CODE_OK
            , [
                "message"       => $this->translator->translate("success")
                , "folder"      => $folder
                , "parent_edge" => $parentEdge
            ]
        );

    }

    private function isValid($value): bool {
        if (null === $value) return false;
        if ("" === trim($value)) return false;
        return true;
    }

    private function getParentEdge($parent, IToken $token): ?Node {

        if (Application::ROOT_FOLDER === $parent) {
            return $this->nodeRepository->getRootForUser($token->getUser());
        }

        return $this->nodeRepository->getNode((int) $parent);
    }

}