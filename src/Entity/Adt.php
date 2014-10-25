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

namespace PhpDA\Entity;

use PhpParser\Node;

class Adt
{
    const GLOBAL_NAMESPACE = '\\';

    /** @var Node\Name */
    private $declaredNamespace;

    /** @var Node\Name[] */
    private $usedNamespaces = array();

    /** @var Node\Name[] */
    private $unsupportedStmts = array();

    /** @var Node\Name[] */
    private $namespacedStrings = array();

    public function __construct()
    {
        $this->declaredNamespace = new Node\Name(self::GLOBAL_NAMESPACE);
    }

    /**
     * @param Node\Name $name
     * @return void
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
    public function hasDeclaredNamespace()
    {
        return $this->getDeclaredNamespace()->toString() !== self::GLOBAL_NAMESPACE;
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
        $usedNamespaces = array();
        foreach ($this->usedNamespaces as $namespace) {
            /** @var Node\Name $namespace */
            if ($namespace->toString() !== $this->getDeclaredNamespace()->toString()) {
                $usedNamespaces[] = $namespace;
            }
        }

        return $usedNamespaces;
    }

    /**
     * @param Node\Name $unsupportedStmt
     * @return void
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
     * @return void
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
}
