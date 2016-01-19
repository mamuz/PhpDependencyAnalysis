<?php
/**
 * The MIT License (MIT)
 *
 * Copyright © 2016 Alexander Ustimenko
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

use PhpDA\Parser\Visitor\AbstractVisitor;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Param;

/**
 * Renames some long-namespaced classes into shorter versions
 *
 * @see phpda.yml.dist for example config
 */
class TypeRenamer extends AbstractVisitor
{

    private $rename = array();

    public function setOptions(array $options)
    {
        parent::setOptions($options);
        if (isset($options['rename']) && is_array($options['rename'])) {
            $this->rename = $options['rename'];
        }
    }

    public function enterNode(Node $node)
    {
        if (empty($this->rename)) {
            return;
        }
        if (isset($node->namespacedName)) {
            $this->rename($node->namespacedName);
        }
        if (isset($node->extends)) {
            $this->rename($node->extends);
        }
        if (isset($node->class)) {
            $this->rename($node->class);
        }
        if ($node instanceof Class_) {
            $node->name = $this->renameString($node->name);
            foreach ($node->implements as $name) {
                $this->rename($name);
            }
        }
        if ($node instanceof Param) {
            $this->rename($node->type);
        }
    }

    private function rename($name)
    {
        if ($name instanceof Node\Name) {
            $name->set($this->renameString($name->toString()));
        }
    }

    private function renameString($name)
    {
        return str_replace(array_keys($this->rename), array_values($this->rename), $name);;
    }
}
