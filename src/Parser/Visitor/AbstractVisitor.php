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

namespace PhpDA\Parser\Visitor;

use PhpDA\Entity\AdtAwareInterface;
use PhpDA\Entity\AdtAwareTrait;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

abstract class AbstractVisitor extends NodeVisitorAbstract implements AdtAwareInterface
{
    use AdtAwareTrait;

    /**
     * @param Node      $node
     * @param Node\Name $name
     * @return void
     */
    protected function exchange(Node $node, Node\Name $name)
    {
        $attributes = $node->getAttributes();
        foreach ($attributes as $attr => $value) {
            $name->setAttribute($attr, $value);
        }
    }

    /**
     * @param Node\Name $name
     * @return void
     */
    protected function setDeclaredNamespace(Node\Name $name)
    {
        $this->getAdt()->setDeclaredNamespace($name);
    }

    /**
     * @param Node\Name $name
     * @return void
     */
    protected function addUsedNamespace(Node\Name $name)
    {
        $this->getAdt()->addUsedNamespace($name);
    }

    /**
     * @param Node\Name $name
     * @return void
     */
    protected function addUnsupportedStmt(Node\Name $name)
    {
        $this->getAdt()->addUnsupportedStmt($name);
    }

    /**
     * @param Node\Name $name
     * @return void
     */
    protected function addNamespacedString(Node\Name $name)
    {
        $this->getAdt()->addNamespacedString($name);
    }
}
