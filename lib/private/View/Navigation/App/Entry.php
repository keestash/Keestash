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

namespace Keestash\View\Navigation\App;

class Entry {

    /** @var string */
    private $title;
    /** @var string */
    private $selector;
    /** @var string|null */
    private $href;

    public function __construct(
        string $title = ""
        , string $selector = ""
        , ?string $href = null
    ) {
        $this->setTitle($title);
        $this->setSelector($selector);
        $this->setHref($href);
    }

    /**
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getSelector(): string {
        return $this->selector;
    }

    /**
     * @param string $selector
     */
    public function setSelector(string $selector): void {
        $this->selector = $selector;
    }

    /**
     * @return string|null
     */
    public function getHref(): ?string {
        return $this->href;
    }

    /**
     * @param string|null $href
     */
    public function setHref(?string $href): void {
        $this->href = $href;
    }


}
