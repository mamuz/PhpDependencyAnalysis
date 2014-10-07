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

use PhpParser\Error;
use PhpParser\Node;

class Analysis
{
    /** @var Error */
    private $parseError;

    /** @var Node[] */
    private $stmts = array();

    /** @var Node/Name */
    private $declaredNamespace;

    /** @var Node/Name[] */
    private $usedNamespaces = array();

    public function __construct()
    {
        $this->declaredNamespace = new Node\Name('\\');
    }

    /**
     * @param Error $error
     */
    public function setParseError(Error $error)
    {
        $this->parseError = $error;
    }

    /**
     * @return Error|null
     */
    public function getParseError()
    {
        return $this->parseError;
    }

    /**
     * @return bool
     */
    public function hasParseError()
    {
        return $this->parseError instanceof Error;
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
     * @param Node\Name $usedNamespace
     */
    public function addUsedNamespace(Node\Name $usedNamespace)
    {
        $this->usedNamespaces[] = $usedNamespace;
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
     * @param Node  $stmt
     * @param mixed $type
     */
    public function addStmt(Node $stmt, $type)
    {
        if (!array_key_exists($type, $this->stmts)) {
            $this->stmts[$type] = array();
        }

        $this->stmts[$type][] = $stmt;
    }
}
