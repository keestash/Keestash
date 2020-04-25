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

namespace KSP\Core\DTO\Encryption;

use doganoo\PHPUtil\Log\FileLogger;
use Keestash;
use KSA\PasswordManager\Exception\KeyNotFoundException;
use KSP\Core\DTO\User\IUser;
use KSP\Core\DTO\Encryption\ICredential;
use KSP\Core\Repository\EncryptionKey\IEncryptionKeyRepository;

class ServerKey implements ICredential {

    private $user           = null;
    private $baseEncryption = null;
    /** @var null|string $secret */
    private $secret               = null;
    private $encryptionRepository = null;

    public function __construct(
        IUser $user
        , IEncryptionKeyRepository $encryptionKeyRepository
    ) {
        $this->encryptionRepository = $encryptionKeyRepository;
        $this->user                 = $user;
        $this->baseEncryption       = Keestash::getServer()->getBaseEncryption($this->user);
    }

    public function getSecret(): string {
        $this->prepareKey();
        return $this->secret;
    }

    private function prepareKey(): void {
        if (null !== $this->secret) return;
        $key = $this->encryptionRepository->getKey($this->user);

        if (null === $key) {
            throw new KeyNotFoundException("could not find key for {$this->user->getId()}");
        }

        $this->secret = $this->baseEncryption->decrypt($key->getValue());
    }

}
