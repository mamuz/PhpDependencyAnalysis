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

namespace PhpDA\Parser\Visitor\Required;

use PhpDA\Parser\Visitor\AbstractVisitor;
use PhpDA\Parser\Visitor\Feature\DeclaredNamespaceCollectorInterface;
use PhpParser\Error;
use PhpParser\Node;

class DeclaredNamespaceCollector extends AbstractVisitor implements DeclaredNamespaceCollectorInterface
{
    /**
     * {@inheritdoc}
     * @throws \PhpParser\Error
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\ClassLike) {
            if (!$node->name) {
                return;
            }
            if (!$this->getAdt()->hasDeclaredGlobalNamespace()) {
                throw new Error('DeclaredNamespace is already defined', $node->getLine());
            }
            $this->collect(new Node\Name($node->namespacedName), $node);
        }
    }
}
