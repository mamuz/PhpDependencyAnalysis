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

class NamespacedStringCollector extends AbstractVisitor implements NamespacedStringCollectorInterface
{
    const NS = '\\';

    const VALID_CLASSNAME_PATTERN = '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/';

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Scalar\String_) {
            if ($this->match($node)) {
                $namespace = self::NS . $this->trimNS($node->value);
                $name = new Node\Name($namespace);
                $this->collect($name, $node);
            }
        }
    }

    /**
     * @param Node\Scalar\String_ $string
     * @return bool
     */
    private function match(Node\Scalar\String_ $string)
    {
        $string = $string->value;

        if (!preg_match(self::VALID_CLASSNAME_PATTERN, str_replace(self::NS, '', $string))) {
            return false;
        }

        return $this->matchToPsrStandard($string);
    }

    /**
     * @param string $string
     * @return bool
     */
    private function matchToPsrStandard($string)
    {
        $trimmedString = $this->trimNS($string);

        if ($string[0] == self::NS && preg_match(self::VALID_CLASSNAME_PATTERN, $trimmedString)) {
            return true;
        }

        return $this->matchNamespacesBy($trimmedString);
    }

    /**
     * @param string $string
     * @return bool
     */
    private function matchNamespacesBy($string)
    {
        $namespaces = explode(self::NS, $string);

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

    /**
     * @param $string
     * @return string
     */
    private function trimNS($string)
    {
        return ltrim($string, self::NS);
    }
}
