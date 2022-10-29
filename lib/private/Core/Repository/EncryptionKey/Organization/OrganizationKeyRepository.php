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

namespace Keestash\Core\Repository\EncryptionKey\Organization;

use Doctrine\DBAL\Exception;
use doganoo\DI\DateTime\IDateTimeService;
use Keestash\Core\DTO\Encryption\Credential\Key\Key;
use Keestash\Core\Repository\EncryptionKey\KeyRepository;
use Keestash\Exception\KeestashException;
use KSP\Core\Backend\IBackend;
use KSP\Core\DTO\Encryption\Credential\Key\IKey;
use KSP\Core\DTO\Organization\IOrganization;
use KSP\Core\Repository\EncryptionKey\Organization\IOrganizationKeyRepository;
use Psr\Log\LoggerInterface;

class OrganizationKeyRepository extends KeyRepository implements IOrganizationKeyRepository {

    private IDateTimeService $dateTimeService;
    private LoggerInterface          $logger;
    private IBackend         $backend;

    public function __construct(
        IBackend           $backend
        , IDateTimeService $dateTimeService
        , LoggerInterface          $logger
    ) {
        parent::__construct($backend, $dateTimeService, $logger);
        $this->dateTimeService = $dateTimeService;
        $this->logger          = $logger;
        $this->backend         = $backend;
    }

    /**
     * @param IOrganization $organization
     * @param IKey          $key
     * @return IKey
     * @throws Exception
     * @throws KeestashException
     */
    public function storeKey(IOrganization $organization, IKey $key): IKey {
        $key = $this->_storeKey($key);

        $queryBuilder = $this->backend->getConnection()->createQueryBuilder();
        $queryBuilder = $queryBuilder
            ->insert('organization_key')
            ->values(
                [
                    'organization_id' => '?'
                    , 'key_id'        => '?'
                    , 'create_ts'     => '?'
                ]
            )
            ->setParameter(0, $organization->getId())
            ->setParameter(1, $key->getId())
            ->setParameter(2, $this->dateTimeService->toYMDHIS($key->getCreateTs()));

        $queryBuilder->executeStatement();

        return $key;
    }

    public function updateKey(IKey $key): bool {
        return $this->_update($key);
    }

    /**
     * @param IOrganization $organization
     * @return IKey
     * @throws KeestashException
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws Exception
     */
    public function getKey(IOrganization $organization): IKey {
        $queryBuilder = $this->backend->getConnection()->createQueryBuilder();
        $queryBuilder->select(
            [
                'k.id'
                , 'k.value'
                , 'k.create_ts'
            ]
        )
            ->from('`key`', 'k')
            ->join('k', 'organization_key', 'ok', 'k.id = ok.key_id')
            ->where('ok.`organization_id` = ?')
            ->setParameter(0, $organization->getId());

        $result = $queryBuilder->executeQuery();
        $users  = $result->fetchAllAssociative();

        $key = null;
        foreach ($users as $row) {
            $key = new Key();
            $key->setId((int) $row['id']);
            $key->setSecret((string) $row['value']);
            $key->setCreateTs($this->dateTimeService->fromFormat((string) $row['create_ts']));
            $key->setKeyHolder($organization);
        }

        if (null === $key) {
            throw new KeestashException();
        }

        return $key;
    }

    public function remove(IOrganization $organization): bool {
        $key          = $this->getKey($organization);
        $queryBuilder = $this->backend->getConnection()->createQueryBuilder();
        $queryBuilder->delete('organization_key')
            ->where('key_id = ?')
            ->setParameter(0, $key->getId())
            ->executeStatement();
        return $this->_remove($key);
    }


}
