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

namespace KSA\Settings\Command;

use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayList\ArrayList;
use Keestash\Command\KeestashCommand;
use KSP\Core\DTO\User\IUser;
use KSP\Core\Repository\User\IUserRepository;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Get extends KeestashCommand {

    public const ARGUMENT_NAME_WITH_EVENTS = 'user-id';

    private IUserRepository $userRepository;

    public function __construct(IUserRepository $userRepository) {
        parent::__construct();
        $this->userRepository = $userRepository;
    }

    protected function configure(): void {
        $this->setName("users:get")
            ->setDescription("lists one or all users")
            ->addArgument(
                Get::ARGUMENT_NAME_WITH_EVENTS
                , InputArgument::OPTIONAL | InputArgument::IS_ARRAY
                , 'the user id(s) or none to list all'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $userIds   = (array) $input->getArgument(Get::ARGUMENT_NAME_WITH_EVENTS);
        $userList  = new ArrayList();
        $tableRows = [];

        if ([] === $userIds) {
            $userList = $this->userRepository->getAll();
        } else {
            foreach ($userIds as $id) {
                $userList->add(
                    $this->userRepository->getUserById((string) $id)
                );
            }
        }

        /** @var IUser $user */
        foreach ($userList as $user) {
            $tableRows[] = [
                $user->getId()
                , $user->getName()
                , $user->getHash()
            ];
        }
        $table = new Table($output);
        $table
            ->setHeaders(['ID', 'Name', 'Hash'])
            ->setRows($tableRows);
        $table->render();

        return KeestashCommand::RETURN_CODE_RAN_SUCCESSFUL;
    }

}