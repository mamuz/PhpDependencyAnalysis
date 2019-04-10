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
use PhpParser\Node;

class MetaNamespaceCollector extends AbstractVisitor
{
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Class_) {
            $this->collectMetaOfClass($node);
        } elseif ($node instanceof Node\Stmt\Interface_) {
            $this->collectMetaOfInterface($node);
        } elseif ($node instanceof Node\Stmt\TraitUse) {
            $this->collectMetaOfTraitUse($node);
        } elseif ($node instanceof Node\Stmt\Trait_) {
            $this->getAdtMeta()->setTrait();
        }
    }

    /**
     * @return \PhpDA\Entity\Meta
     */
    private function getAdtMeta()
    {
        return $this->getAdt()->getMeta();
    }

    /**
     * @param Node\Stmt\Class_ $node
     */
    private function collectMetaOfClass(Node\Stmt\Class_ $node)
    {
        $this->getAdtMeta()->setClass();

        if ($node->isAbstract()) {
            $this->getAdtMeta()->setAbstract();
        }
        if ($node->isFinal()) {
            $this->getAdtMeta()->setFinal();
        }

        foreach ($node->implements as $name) {
            if ($name = $this->filter($name)) {
                $this->getAdtMeta()->addImplementedNamespace($name);
            }
        }

        $this->collectExtends($node->extends);
    }

    /**
     * @param Node\Stmt\Interface_ $node
     */
    private function collectMetaOfInterface(Node\Stmt\Interface_ $node)
    {
        $this->getAdtMeta()->setInterface();
        $this->collectExtends($node->extends);
    }

    /**
     * @param Node\Stmt\TraitUse $node
     */
    private function collectMetaOfTraitUse(Node\Stmt\TraitUse $node)
    {
        foreach ($node->traits as $name) {
            if ($name = $this->filter($name)) {
                $this->getAdtMeta()->addUsedTraitNamespace($name);
            }
        }
    }

    /**
     * @param null|Node\Name|Node\Name[] $extends
     */
    private function collectExtends($extends)
    {
        if (empty($extends)) {
            return;
        }

        if (!is_array($extends)) {
            $extends = [$extends];
        }

        foreach ($extends as $name) {
            if ($name = $this->filter($name)) {
                $this->getAdtMeta()->addExtendedNamespace($name);
            }
        }
    }
}
