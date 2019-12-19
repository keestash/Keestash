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

namespace Keestash\Core\Service;

use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;
use Keestash;
use ReflectionClass;

class ReflectionService {

    /**
     * @param string $className
     * @return object
     */
    public function createObject(string $className) {
        $constructorArgs = [];
        $instance        = new ReflectionClass($className);

        if (null !== $instance->getConstructor()) {
            foreach ($instance->getConstructor()->getParameters() as $parameter) {
                if (true === $parameter->isDefaultValueAvailable()) continue; // TODO validate ?!
                $className         = $parameter->getClass()->getName();
                $class             = Keestash::getServer()->query($className);
                $constructorArgs[] = $class;
            }
        }

        return $instance->newInstanceArgs($constructorArgs);
    }

    /**
     * @param object $class
     * @return ArrayList
     */
    public function getParentClasses($class): ArrayList {
        $list = new ArrayList();
        $p    = class_parents($class);
        $list->addAllArray($p);
        return $list;
    }


}