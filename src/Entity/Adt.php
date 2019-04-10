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

namespace PhpDA\Entity;

use PhpParser\Node;

class Adt
{
    const GLOBAL_NAMESPACE = '\\';

    /** @var Meta */
    private $meta;

    /** @var Node\Name */
    private $declaredNamespace;

    /** @var Node\Name[] */
    private $usedNamespaces = [];

    /** @var Node\Name[] */
    private $unsupportedStmts = [];

    /** @var Node\Name[] */
    private $namespacedStrings = [];

    public function __construct()
    {
        $this->declaredNamespace = new Node\Name(self::GLOBAL_NAMESPACE);
        $this->meta = new Meta;
    }

    /**
     * @return Meta
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param Node\Name $name
     */
    public function setDeclaredNamespace(Node\Name $name)
    {
        $this->declaredNamespace = $name;
    }

    /**
     * @return Node\Name
     */
    public function getDeclaredNamespace()
    {
        return $this->declaredNamespace;
    }

    /**
     * @return bool
     */
    public function hasDeclaredGlobalNamespace()
    {
        return $this->getDeclaredNamespace()->toString() === self::GLOBAL_NAMESPACE;
    }

    /**
     * @param Node\Name $usedNamespace
     */
    public function addUsedNamespace(Node\Name $usedNamespace)
    {
        $this->usedNamespaces[$usedNamespace->toString()] = $usedNamespace;
    }

    /**
     * @return Node\Name[]
     */
    public function getUsedNamespaces()
    {
        return $this->disjoin($this->usedNamespaces, $this->getUnsupportedStmtNamespaces());
    }

    /**
     * @return Node\Name[]
     */
    public function getCalledNamespaces()
    {
        return $this->disjoin($this->getUsedNamespaces(), $this->getDeclaredAndMetaNamespaces());
    }

    /**
     * @param Node\Name $unsupportedStmt
     */
    public function addUnsupportedStmt(Node\Name $unsupportedStmt)
    {
        $this->unsupportedStmts[$unsupportedStmt->toString()] = $unsupportedStmt;
    }

    /**
     * @return Node\Name[]
     */
    public function getUnsupportedStmts()
    {
        return $this->unsupportedStmts;
    }

    /**
     * @param Node\Name $namespacedString
     */
    public function addNamespacedString(Node\Name $namespacedString)
    {
        $this->namespacedStrings[$namespacedString->toString()] = $namespacedString;
    }

    /**
     * @return Node\Name[]
     */
    public function getNamespacedStrings()
    {
        return $this->namespacedStrings;
    }

    /**
     * @param Node\Name[] $set
     * @param string[]    $complement
     * @return Node\Name[]
     */
    private function disjoin(array $set, array $complement)
    {
        $diff = [];

        foreach ($set as $node) {
            $namespace = $node->toString();
            if (!in_array($namespace, $complement)) {
                $diff[$namespace] = $node;
            }
        }

        return $diff;
    }

    /**
     * @return string[]
     */
    private function getUnsupportedStmtNamespaces()
    {
        return array_keys($this->getUnsupportedStmts());
    }

    /**
     * @return string[]
     */
    private function getDeclaredAndMetaNamespaces()
    {
        $namespaceStrings = array_keys($this->getMeta()->getAllNamespaces());
        $namespaceStrings[] = $this->getDeclaredNamespace()->toString();

        return $namespaceStrings;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $meta = $this->getMeta()->toArray();
        $meta['implementedNamespaces'] = $this->stringify($meta['implementedNamespaces']);
        $meta['extendedNamespaces'] = $this->stringify($meta['extendedNamespaces']);
        $meta['usedTraitNamespaces'] = $this->stringify($meta['usedTraitNamespaces']);

        return [
            'meta'              => $meta,
            'usedNamespaces'    => $this->stringify($this->getUsedNamespaces()),
            'unsupportedStmts'  => $this->stringify($this->getUnsupportedStmts()),
            'namespacedStrings' => $this->stringify($this->getNamespacedStrings()),
        ];
    }

    /**
     * @param Node\Name[] $names
     * @return array
     */
    private function stringify(array $names)
    {
        foreach ($names as $key => $name) {
            $names[$key] = $name->toString();
        }

        return $names;
    }
}
