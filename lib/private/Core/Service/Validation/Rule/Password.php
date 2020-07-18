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

namespace Keestash\Core\Service\Validation\Rule;

use Keestash;
use Keestash\Core\Service\User\UserService;
use Laminas\Validator\AbstractValidator;

class Password extends AbstractValidator {

    private const PASSWORD = "password";
    protected $messageTemplates = [
        Password::PASSWORD => "%password% does not match the minimum requirements"
    ];
    /** @var UserService */
    private $userService;

    public function __construct() {
        parent::__construct(null);
        $this->userService = Keestash::getServer()->query(UserService::class);
    }

    public function isValid($value) {
        $this->setValue($value);

        if (false === $this->userService->passwordHasMinimumRequirements($value)) {
            $this->error(Password::PASSWORD);
            return false;
        }

        return true;
    }

}
