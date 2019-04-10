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

namespace PhpDA\Parser;

use PhpParser\Node\Name;

class NameContext extends \PhpParser\NameContext
{
    /** @var array */
    private $namespaceAliases = [];

    public function addAlias(Name $name, string $aliasName, int $type, array $errorAttrs = [])
    {
        parent::addAlias($name, $aliasName, $type);

        // if ($name instanceof UseUse) {
        //     $aliasName = $name->alias;
        //     $type |= $name->type;
        //
        //     if (isset($this->aliases[$type][$aliasName])) {
        //         $this->namespaceAliases[$aliasName] = (string) $this->aliases[$type][$aliasName];
        //     } elseif (isset($this->aliases[$type][strtolower($aliasName)])) {
        //         $this->namespaceAliases[$aliasName] = (string) $this->aliases[$type][strtolower($aliasName)];
        //     }
        // }
        if (isset($this->origAliases[$type][$aliasName])) {
            $this->namespaceAliases[$aliasName] = (string) $this->origAliases[$type][$aliasName];
        }
    }

    /**
     * @return \PhpParser\Node\Name[][]
     */
    public function getNamespaceAliases()
    {
        return $this->namespaceAliases;
    }
}
