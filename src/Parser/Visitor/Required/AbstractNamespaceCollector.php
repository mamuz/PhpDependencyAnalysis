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

namespace PhpDA\Parser\Visitor\Required;

use PhpDA\Parser\Visitor\AbstractVisitor;
use PhpDA\Plugin\ConfigurableInterface;
use PhpParser\Node;

abstract class AbstractNamespaceCollector extends AbstractVisitor implements ConfigurableInterface
{
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

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Name) {
            if (!$this->ignores($node)) {
                $node = $this->filter($node);
                $this->getAdt()->setDeclaredNamespace($node);
            }
        }
    }

    /**
     * @param Node\Name $name
     * @return bool
     */
    protected function ignores(Node\Name $name)
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
    protected function filter(Node\Name $name)
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

    /**
     * @param Node\Name $name
     * @return void
     */
    abstract protected function bind(Node\Name $name);
}
