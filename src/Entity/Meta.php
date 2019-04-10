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

class Meta
{
    const TYPE_INTERFACE = 'interface';

    const TYPE_CLASS = 'class';

    const TYPE_TRAIT = 'trait';

    /** @var string */
    private $type = '';

    /** @var Node\Name */
    private $implementedNamespaces = [];

    /** @var Node\Name */
    private $extendedNamespaces = [];

    /** @var Node\Name */
    private $usedTraitNamespaces = [];

    /** @var boolean */
    private $isAbstract = false;

    /** @var boolean */
    private $isFinal = false;

    public function setInterface()
    {
        $this->type = self::TYPE_INTERFACE;
    }

    public function isInterface()
    {
        return $this->type === self::TYPE_INTERFACE;
    }

    public function setClass()
    {
        $this->type = self::TYPE_CLASS;
    }

    public function isClass()
    {
        return $this->type === self::TYPE_CLASS;
    }

    public function setTrait()
    {
        $this->type = self::TYPE_TRAIT;
    }

    public function isTrait()
    {
        return $this->type === self::TYPE_TRAIT;
    }

    public function setAbstract()
    {
        $this->isAbstract = true;
    }

    public function isAbstract()
    {
        return $this->isAbstract;
    }

    public function setFinal()
    {
        $this->isFinal = true;
    }

    public function isFinal()
    {
        return $this->isFinal;
    }

    /**
     * @param Node\Name $implementedNamespace
     */
    public function addImplementedNamespace(Node\Name $implementedNamespace)
    {
        $this->implementedNamespaces[$implementedNamespace->toString()] = $implementedNamespace;
    }

    /**
     * @return Node\Name[]
     */
    public function getImplementedNamespaces()
    {
        return $this->implementedNamespaces;
    }

    /**
     * @param Node\Name $extendedNamespace
     */
    public function addExtendedNamespace(Node\Name $extendedNamespace)
    {
        $this->extendedNamespaces[$extendedNamespace->toString()] = $extendedNamespace;
    }

    /**
     * @return Node\Name[]
     */
    public function getExtendedNamespaces()
    {
        return $this->extendedNamespaces;
    }

    /**
     * @param Node\Name $usedTraitNamespace
     */
    public function addUsedTraitNamespace(Node\Name $usedTraitNamespace)
    {
        $this->usedTraitNamespaces[$usedTraitNamespace->toString()] = $usedTraitNamespace;
    }

    /**
     * @return Node\Name[]
     */
    public function getUsedTraitNamespaces()
    {
        return $this->usedTraitNamespaces;
    }

    /**
     * @return Node\Name[]
     */
    public function getAllNamespaces()
    {
        return $this->getUsedTraitNamespaces()
               + $this->getExtendedNamespaces()
               + $this->getImplementedNamespaces();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }
}
