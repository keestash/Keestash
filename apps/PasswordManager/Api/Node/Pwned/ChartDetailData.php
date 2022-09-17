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

namespace KSA\PasswordManager\Api\Node\Pwned;

use doganoo\PHPAlgorithms\Datastructure\Set\HashSet;
use doganoo\PHPAlgorithms\Datastructure\Table\HashTable;
use Keestash\Api\Response\JsonResponse;
use KSA\PasswordManager\Entity\Node\Pwned\Breaches;
use KSA\PasswordManager\Entity\Node\Pwned\Passwords;
use KSA\PasswordManager\Repository\Node\NodeRepository;
use KSA\PasswordManager\Repository\Node\PwnedBreachesRepository;
use KSA\PasswordManager\Repository\Node\PwnedPasswordsRepository;
use KSP\Api\IResponse;
use KSP\Core\DTO\Token\IToken;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ChartDetailData implements RequestHandlerInterface {

    private PwnedBreachesRepository  $pwnedBreachesRepository;
    private PwnedPasswordsRepository $pwnedPasswordsRepository;
    private NodeRepository           $nodeRepository;

    public function __construct(
        PwnedBreachesRepository    $pwnedBreachesRepository
        , PwnedPasswordsRepository $pwnedPasswordsRepository
        , NodeRepository           $nodeRepository
    ) {
        $this->pwnedBreachesRepository  = $pwnedBreachesRepository;
        $this->pwnedPasswordsRepository = $pwnedPasswordsRepository;
        $this->nodeRepository           = $nodeRepository;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface {
        /** @var IToken|null $token */
        $token  = $request->getAttribute(IToken::class);
        $result = [];
        $cache  = new HashTable();

        if (null === $token) {
            return new JsonResponse(['user not found'], IResponse::NOT_FOUND);
        }

        $root = $this->nodeRepository->getRootForUser($token->getUser());

        $passwords     = $this->pwnedPasswordsRepository->getPwnedByNode($root, 1);
        $breachesTable = $this->pwnedBreachesRepository->getPwnedByNode($root);

        foreach ($passwords->keySet() as $key) {
            /** @var Passwords $password */
            $password = $passwords->get($key);

            if ($cache->contains($password->getNodeId())) {
                $node = $cache->get($password->getNodeId());
            } else {
                $node = $this->nodeRepository->getNode($password->getNodeId(), 0, 1);
                $cache->put($password->getNodeId(), $node);
            }

            $result['passwords'][] = [
                'node'       => [
                    'id'     => $node->getId()
                    , 'name' => $node->getName()
                ]
                , 'severity' => $password->getSeverity()
            ];
        }

        foreach ($breachesTable->keySet() as $key) {
            /** @var Breaches $breaches */
            $breaches = $breachesTable->get($key);

            if (null === $breaches->getHibpData()) {
                continue;
            }

            if ($cache->contains($breaches->getNodeId())) {
                $node = $cache->get($breaches->getNodeId());
            } else {
                $node = $this->nodeRepository->getNode($breaches->getNodeId(), 0, 1);
                $cache->put($breaches->getNodeId(), $node);
            }

            $result['breaches'][] = [
                'node'   => [
                    'id'     => $node->getId()
                    , 'name' => $node->getName()
                ]
                , 'hibp' => $this->getHibpData($breaches->getHibpData())
            ];
        }
        return new JsonResponse(
            $result
            , IResponse::OK
        );
    }

    private function getHibpData(array $data): array {
        $dataClassesSet = new HashSet();
        $platforms      = new HashSet();
        foreach ($data as $d) {
            $dataClassesSet->addAll($d['DataClasses']);
            $platforms->add($d['Title']);
        }

        return [
            'types'       => $dataClassesSet->toArray()
            , 'platforms' => $platforms->toArray()
        ];
    }

}