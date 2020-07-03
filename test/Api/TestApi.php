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

namespace KST\Api;

use Keestash\Api\AbstractApi;
use KSP\Core\DTO\IToken;
use KSP\L10N\IL10N;

class TestApi extends AbstractApi {

    private $parameters = null;

    public function __construct(
        IL10N $l10n
        , ?IToken $token = null
    ) {
        parent::__construct($l10n, $token);
    }

    public function onCreate(array $parameters): void {
        $this->parameters = $parameters;
    }

    public function create(): void {
    }

    public function afterCreate(): void {

    }

    public function getParameters(): array {
        return $this->parameters;
    }

}
