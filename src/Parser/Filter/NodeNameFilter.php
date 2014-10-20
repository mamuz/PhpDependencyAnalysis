<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 Marco Muths
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

namespace PhpDA\Parser\Filter;

use PhpParser\Node;

class NodeNameFilter implements NodeNameFilterInterface
{
    /** @var array */
    private $ignoredNamespaces = array('self', 'parent', 'static', 'null', 'true', 'false');

    /** @var int|null */
    private $sliceOffset;

    /** @var int|null */
    private $sliceLength;

    /** @var int */
    private $minDepth = 0;

    /** @var string */
    private $excludePattern;

    public function setOptions(array $options)
    {
        if (isset($options['sliceOffset'])) {
            $this->sliceOffset = (int) $options['sliceOffset'];
        }

        if (isset($options['sliceLength'])) {
            $this->sliceLength = (int) $options['sliceLength'];
        }

        if (isset($options['minDepth'])) {
            $this->minDepth = (int) $options['minDepth'];
        }

        if (isset($options['excludePattern'])) {
            $this->excludePattern = (string) $options['excludePattern'];
        }
    }

    public function filter(Node\Name $name)
    {
        if ($this->ignores($name)) {
            return null;
        }

        if ($this->excludes($name)) {
            return null;
        }

        return $this->slice($name);
    }

    /**
     * @param Node\Name $name
     * @return bool
     */
    private function ignores(Node\Name $name)
    {
        return in_array(strtolower($name->toString()), $this->ignoredNamespaces);
    }

    /**
     * @param Node\Name $name
     * @return bool
     */
    private function excludes(Node\Name $name)
    {
        $isIgnored = false;

        if ($this->minDepth > 0) {
            $isIgnored = count($name->parts) < $this->minDepth;
        }

        if (!$isIgnored && $this->excludePattern) {
            $isIgnored = (bool) preg_match($this->excludePattern, $name->toString());
        }

        return $isIgnored;
    }

    /**
     * @param Node\Name $name
     * @return Node\Name
     */
    private function slice(Node\Name $name)
    {
        if (is_null($this->sliceOffset) && is_null($this->sliceLength)) {
            return $name;
        }

        if (is_null($this->sliceLength)) {
            $parts = array_slice($name->parts, (int) $this->sliceOffset);
        } else {
            $parts = array_slice($name->parts, (int) $this->sliceOffset, (int) $this->sliceLength);
        }

        return new Node\Name($parts);
    }
}
