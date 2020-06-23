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

namespace Keestash\Core\Service\Encryption\ServerKey;

use DateTime;
use doganoo\PHPUtil\Util\StringUtil;
use Keestash;
use Keestash\Core\DTO\Encryption\Key\ServerKey;
use Keestash\Core\Service\Encryption\Encryption\KeestashEncryptionService;
use KSA\PasswordManager\Exception\KeyNotFoundException;
use KSP\Core\DTO\User\IUser;
use KSP\Core\Repository\EncryptionKey\IEncryptionKeyRepository;

/**
 * Class ServerKeyService
 *
 * @package Keestash\Core\Service\Encryption\ServerKey
 * @author  Dogan Ucar <dogan@dogan-ucar.de>
 */
class ServerKeyService {

    /** @var IEncryptionKeyRepository */
    private $encryptionKeyRepository;

    /**
     * ServerKeyService constructor.
     *
     * @param IEncryptionKeyRepository $encryptionKeyRepository
     */
    public function __construct(IEncryptionKeyRepository $encryptionKeyRepository) {
        $this->encryptionKeyRepository = $encryptionKeyRepository;
    }

    /**
     * returns an instance of ServerKey for a given user
     *
     * @param IUser $user
     *
     * @return ServerKey
     * @throws KeyNotFoundException
     */
    public function getKeyForUser(IUser $user): ServerKey {
        $key = $this->encryptionKeyRepository->getKey($user);

        if (null === $key) {
            throw new KeyNotFoundException("could not find key for {$user->getId()}");
        }

        $serverKey = new ServerKey();
        $serverKey->setId($key->getId());
        $serverKey->setCreateTs($key->getCreateTs());
        $serverKey->setValue($key->getValue());
        return $serverKey;
    }

    public function createKey(KeestashEncryptionService $baseEncryption, IUser $user): ?ServerKey {
        // Step 1: we create a random secret
        //      This secret consists of a unique id (uuid)
        //      and a hash created out of the user object
        $secret = StringUtil::getUUID() . json_encode($user);
        // Step 2: we encrypt the data with our base encryption
        $secret = $baseEncryption->encrypt($secret);
        // Step 3: we add the data to the database

        $key = new ServerKey();
        $key->setValue($secret);
        $key->setCreateTs(new DateTime());

        $added   = $this->encryptionKeyRepository->storeKey(
            $user
            , $key
        );
        $created = (true === $added) && (true === is_string($secret));

        if (false === $created) return null;
        return $key;
    }

}
