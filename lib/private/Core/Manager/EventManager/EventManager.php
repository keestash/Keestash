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

namespace Keestash\Core\Manager\EventManager;

use DateTimeImmutable;
use Keestash\Core\DTO\Queue\EventMessage;
use Keestash\Core\DTO\Queue\Stamp;
use KSP\Core\DTO\Queue\IMessage;
use KSP\Core\Manager\EventManager\IEvent;
use KSP\Core\Manager\EventManager\IEventManager;
use KSP\Core\Repository\Queue\IQueueRepository;
use Laminas\Serializer\Adapter\PhpSerialize;
use Ramsey\Uuid\Uuid;

class EventManager implements IEventManager {

    private IQueueRepository $queueRepository;
    private array            $listeners;

    public function __construct(IQueueRepository $queueRepository) {
        $this->queueRepository = $queueRepository;
    }

    public function execute(IEvent $event): void {
        $listeners  = $this->listeners[get_class($event)];
        $serializer = new PhpSerialize();

        foreach ($listeners as $listener) {

            if (false === is_string($listener)) {
                continue;
            }

            $message = new EventMessage();
            $message->setId((string) Uuid::uuid4());
            $message->setType(IMessage::TYPE_EVENT);
            $message->setPayload(
                [
                    'listener' => $listener
                    , 'event'  => [
                    'serialized' => $serializer->serialize($event)
                    , 'name'     => get_class($event)
                ]
                ]
            );
            $message->setReservedTs(new DateTimeImmutable());
            $message->setAttempts(0);
            $message->setPriority(1);
            $message->setCreateTs(new DateTimeImmutable());

            $stamp = new Stamp();
            $stamp->setCreateTs(new DateTimeImmutable());
            $stamp->setName($listener);
            $stamp->setValue((string) Uuid::uuid4());
            $message->addStamp($stamp);

            $this->queueRepository->insert($message);

        }

    }

    public function registerAll(array $events): void {
        foreach ($events as $event => $listeners) {
            foreach ($listeners as $listener) {
                $this->register($event, $listener);
            }
        }
    }

    public function register(string $event, string $listener): void {
        $this->listeners[$event][] = $listener;
    }

}
