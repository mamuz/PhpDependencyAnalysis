<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2014 Marco Muths
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace PhpDA\Parser\Visitor;

use PhpParser\Node;

class NamespacedStringCollector extends AbstractVisitor
{
    const VALID_CLASSNAME_PATTERN = '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/';

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Scalar\String) {
            if ($this->match($node)) {
                $this->collect($node);
            }
        }
    }

    /**
     * @param Node\Scalar\String $string
     * @return bool
     */
    private function match(Node\Scalar\String $string)
    {
        $string = $string->value;

        if (!preg_match(self::VALID_CLASSNAME_PATTERN, str_replace('\\', '', $string))) {
            return false;
        }

        if ($this->matchToPsrStandard($string)) {
            return true;
        }

        if ($this->matchToPearStandard($string)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $string
     * @return bool
     */
    private function matchToPsrStandard($string)
    {
        $normalized = ltrim($string, '\\');

        if ($string[0] == '\\' && preg_match(self::VALID_CLASSNAME_PATTERN, $normalized)) {
            return true;
        }

        return $this->matchNamespacesBy('\\', ltrim($normalized, '\\'));
    }

    /**
     * @param string $string
     * @return bool
     */
    private function matchToPearStandard($string)
    {
        return $this->matchNamespacesBy('_', $string);
    }

    /**
     * @param string $glue
     * @param string $string
     * @return bool
     */
    private function matchNamespacesBy($glue, $string)
    {
        $namespaces = explode($glue, $string);

        if (count($namespaces) < 2) {
            return false;
        }

        foreach ($namespaces as $namespace) {
            if (!preg_match(self::VALID_CLASSNAME_PATTERN, $namespace)) {
                return false;
            }
        }

        return true;
    }
}
