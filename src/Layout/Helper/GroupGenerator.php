<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2019 Marco Muths
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace PhpDA\Layout\Helper;

use PhpParser\Node\Name;

class GroupGenerator
{
    /** @var array */
    private $groups = [];

    /** @var int */
    private $groupLength = 0;

    /**
     * @param int $groupLength
     */
    public function setGroupLength($groupLength)
    {
        $this->groupLength = (int) $groupLength;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param Name $name
     * @return int|null
     */
    public function getIdFor(Name $name)
    {
        if ($this->groupLength < 1) {
            return null;
        }

        $group = $this->generateGroupNameBy($name->parts);

        if (!$this->hasIdFor($group)) {
            return $this->generateIdFor($group);
        }

        return $this->searchIdFor($group);
    }

    /**
     * @param array $namespaceParts
     * @return string
     */
    private function generateGroupNameBy(array $namespaceParts)
    {
        return implode('\\', array_slice($namespaceParts, 0, $this->groupLength));
    }

    /**
     * @param string $group
     * @return boolean
     */
    private function hasIdFor($group)
    {
        return in_array($group, $this->groups);
    }

    /**
     * @param string $group
     * @return int
     */
    private function generateIdFor($group)
    {
        $id = (sizeof($this->groups) + 1) * -1;
        $this->groups[$id] = $group;

        return $id;
    }

    /**
     * @param string $group
     * @return int
     */
    private function searchIdFor($group)
    {
        return array_search($group, $this->groups);
    }
}
