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

namespace Keestash\Core\Service\Queue;

use doganoo\DI\DateTime\IDateTimeService;
use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayList\ArrayList;
use JsonException;
use Keestash\Core\DTO\Queue\EventMessage;
use KSP\Core\Repository\Queue\IQueueRepository;
use KSP\Core\Service\Encryption\IBase64Service;
use KSP\Core\Service\Queue\IQueueService;
use Psr\Log\LoggerInterface;

class QueueService implements IQueueService {

    private IQueueRepository $queueRepository;
    private IDateTimeService $dateTimeService;
    private IBase64Service   $base64Service;
    private LoggerInterface  $logger;

    public function __construct(
        IQueueRepository   $queueRepository
        , IDateTimeService $dateTimeService
        , IBase64Service   $base64Service
        , LoggerInterface  $logger
    ) {
        $this->queueRepository = $queueRepository;
        $this->dateTimeService = $dateTimeService;
        $this->base64Service   = $base64Service;
        $this->logger          = $logger;
    }

    public function getQueue(bool $forceAll = false): ArrayList {

        $messageList = new ArrayList();
        if (true === $forceAll) {
            $messages = $this->queueRepository->getQueue();
        } else {
            $messages = $this->queueRepository->getSchedulableMessages();
        }

        /** @var array $messageArray */
        foreach ($messages as $messageArray) {
            try {
                $message = new EventMessage();
                $message->setId((string) $messageArray["id"]);
                $message->setCreateTs(
                    $this->dateTimeService->fromFormat((string) $messageArray["create_ts"])
                );
                $message->setPriority((int) $messageArray["priority"]);
                $message->setAttempts((int) $messageArray["attempts"]);
                $message->setReservedTs(
                    $this->dateTimeService->fromFormat((string) $messageArray["reserved_ts"])
                );
                $message->setPayload(
                    $this->base64Service->decryptArrayRecursive(
                        (array) json_decode(
                            (string) $messageArray["payload"]
                            , true
                            , 512
                            , JSON_THROW_ON_ERROR
                        )
                    )
                );

//                $stamps       = (array) json_decode(
//                    $messageArray['stamps']
//                    , true
//                    , 512
//                    , JSON_THROW_ON_ERROR
//                );
//                $stampObjects = [];
//                /**
//                 * @var int    $key
//                 * @var  array $stamp
//                 */
//                foreach ($stamps as $key => $stamp) {
//                    $stampObject = new Stamp();
//                    $stampObject->setName($stamp['name']);
//                    $stampObject->setValue($stamp['value']);
//                    $stampObject->setCreateTs(
//                        $this->dateTimeService->fromFormat((string) $stamp['create_ts']['date'])
//                    );
//                    $stampObjects[$key] = $stampObject;
//                }
//                $message->setStamps(
//                    HashTable::fromIterable($stampObjects)
//                );
                $messageList->add($message);
            } catch (JsonException $exception) {
                $this->logger->error(
                    'error parsing payload or stamps'
                    , [
                        'exception' => $exception
                        , 'message' => $messageArray
                    ]
                );
            }
        }
        return $messageList;
    }

    public function remove(string $uuid): void {
        $this->queueRepository->deleteByUuid($uuid);
    }

    public function updateAttempts(string $uuid, int $attempts): void {
        $this->queueRepository->updateAttempts($uuid, $attempts);
    }

}