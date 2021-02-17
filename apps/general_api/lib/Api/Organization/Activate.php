<?php
declare(strict_types=1);
/**
 * Keestash
 *
 * Copyright (C) <2021> <Dogan Ucar>
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

namespace KSA\GeneralApi\Api\Organization;

use DateTime;
use Keestash\Api\AbstractApi;
use KSA\GeneralApi\Exception\GeneralApiException;
use KSA\GeneralApi\Repository\IOrganizationRepository;
use KSP\Api\IResponse;
use KSP\Core\DTO\Token\IToken;
use KSP\Core\ILogger\ILogger;
use KSP\L10N\IL10N;

class Activate extends AbstractApi {

    private IOrganizationRepository $organizationRepository;
    private ILogger                 $logger;

    public function __construct(
        IOrganizationRepository $organizationRepository
        , IL10N $l10n
        , ILogger $logger
        , ?IToken $token = null
    ) {
        parent::__construct($l10n, $token);
        $this->organizationRepository = $organizationRepository;
        $this->logger                 = $logger;
    }

    public function onCreate(array $parameters): void {

    }

    public function create(): void {

        $id       = $this->getParameter("id");
        $activate = $this->getParameter('activate');
        $activate = $activate === 'true';

        if (null === $id || "" === $id || false === is_numeric($id)) {
            throw new GeneralApiException('no id provided');
        }

        $organization = $this->organizationRepository->get((int) $id);

        if (null === $organization) {
            throw new GeneralApiException('no organization found');
        }

        $organization->setActiveTs(
            true === $activate
                ? new DateTime()
                : null
        );

        $this->organizationRepository->update($organization);

        $this->createAndSetResponse(
            IResponse::RESPONSE_CODE_OK
            , [
                "organization" => $organization
            ]
        );
    }

    public function afterCreate(): void {

    }

}