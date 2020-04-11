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

namespace Keestash\Core\Manager\StringManager;

use doganoo\PHPUtil\Log\FileLogger;
use KSP\Core\DTO\File\IExtension;
use KSP\Core\Manager\StringManager\IStringManager;
use RecursiveDirectoryIterator;
use SplFileInfo;

class StringManager implements IStringManager {

    /** @var array $paths */
    private $paths;

    /** @var array $additional */
    private $additional;

    public function __construct() {
        $this->paths      = [];
        $this->additional = [];
    }

    public function addPath(string $key, string $path): void {

        if (false === is_dir($path)) {
            FileLogger::warn("$path is not a path. Skipping");
            return;
        }

        $this->paths[$key] = $path;
    }

    public function load(?string $key = null): array {

        $paths = null === $key
            ? $this->paths
            : $this->paths[$key];

        $result = [];


        foreach ($paths as $key => $path) {

            $iterator = new RecursiveDirectoryIterator($path);
            /** @var SplFileInfo $info */
            foreach ($iterator as $info) {

                if (strtolower(trim($info->getExtension())) === IExtension::JSON) {

                    $content = $this->mergeStrings(
                        file_get_contents($info->getRealPath())
                        , $this->additional[$key] ?? []
                    );

                    $result[$key] = json_encode($content);
                }
            }
        }
        return $result;
    }

    private function mergeStrings(string $base, array $additional): array {
        $userStrings = json_decode($base, true);
        $strings     = $userStrings["strings"] ?? [];

        $userStrings["strings"] =
            array_merge(
                $strings
                , $additional
            );

        return $userStrings;


    }

    public function addString(string $appId, string $key, string $value): void {
        $this->additional[$appId][$key] = $value;
    }


}