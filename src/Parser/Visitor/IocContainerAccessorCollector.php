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

namespace PhpDA\Parser\Visitor;

use PhpDA\Parser\Visitor\Feature\NamespacedStringCollectorInterface;
use PhpParser\Node;

class IocContainerAccessorCollector extends AbstractVisitor implements NamespacedStringCollectorInterface
{
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Expr\MethodCall
            && $node->name === 'get'
            && count($node->args) > 0
        ) {
            /** @var Node\Arg $arg */
            $arg = array_shift($node->args);
            if ($arg->value instanceof Node\Scalar\String_
                && is_string($arg->value->value)
                && !empty($arg->value->value)
            ) {
                $name = new Node\Name($arg->value->value);
                $this->collect($name, $node);
            }
        }
    }
}
