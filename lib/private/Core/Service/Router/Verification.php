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

namespace Keestash\Core\Service\Router;

use Keestash\Core\Manager\RouterManager\Router\APIRouter;
use KSP\Core\DTO\Token\IToken;
use KSP\Core\DTO\User\IUser;
use KSP\Core\Repository\Token\ITokenRepository;
use KSP\Core\Repository\User\IUserRepository;

class Verification {

    private ITokenRepository $tokenRepository;
    private IUserRepository  $userRepository;

    public function __construct(
        ITokenRepository $tokenRepository
        , IUserRepository $userRepository
    ) {
        $this->tokenRepository = $tokenRepository;
        $this->userRepository  = $userRepository;
    }

    public function verifyToken(array $parameters): ?IToken {

        $tokenString = $parameters[APIRouter::FIELD_NAME_TOKEN] ?? null;
        $userHash    = $parameters[APIRouter::FIELD_NAME_USER_HASH] ?? null;

        if (null === $tokenString) return null;
        if (null === $userHash) return null;

        $users = $this->userRepository->getAll();

        if (0 === $users->length()) return null;

        $hashVerified = false;
        /** @var IUser $user */
        foreach ($users as $user) {
            if ($user->getHash() === $userHash) {
                $hashVerified = true;
                break;
            }
        }

        if (false === $hashVerified) return null;

        $token = $this->tokenRepository->getByHash((string) $tokenString);

        if (null === $token) return null;
        if ($token->getValue() !== $tokenString) return null;
        if (true === $token->expired()) return null;

        return $token;
    }

}
