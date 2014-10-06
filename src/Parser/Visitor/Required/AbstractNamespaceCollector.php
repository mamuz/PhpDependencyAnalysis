<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2014 Marco Muths
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace PhpDA\Parser\Visitor\Required;

use PhpDA\Parser\Visitor\AbstractVisitor;
use PhpDA\Plugin\ConfigurableInterface;
use PhpParser\Node;

abstract class AbstractNamespaceCollector extends AbstractVisitor implements ConfigurableInterface
{
    /** @var int|null */
    private $offset;

    /** @var int|null */
    private $length;

    /** @var int */
    private $minLength = 0;

    public function setOptions(array $options)
    {
        if (isset($options['offset'])) {
            $this->offset = (int) $options['offset'];
        }

        if (isset($options['length'])) {
            $this->length = (int) $options['length'];
        }

        if (isset($options['minLength'])) {
            $this->minLength = (int) $options['minLength'];
        }
    }

    /**
     * @param Node\Name $name
     * @return bool
     */
    protected function ignores(Node\Name $name)
    {
        if ($this->minLength > 0) {
            return count($name->parts) < $this->minLength;
        }

        return false;
    }

    /**
     * @param Node\Name $name
     * @return Node\Name
     */
    protected function filter(Node\Name $name)
    {
        if (is_null($this->offset) && is_null($this->length)) {
            return $name;
        }

        if (is_null($this->length)) {
            $parts = array_slice($name->parts, (int) $this->offset);
        } else {
            $parts = array_slice($name->parts, (int) $this->offset, (int) $this->length);
        }

        return new Node\Name($parts);
    }
}
