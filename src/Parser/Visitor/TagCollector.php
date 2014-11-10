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

namespace PhpDA\Parser\Visitor;

use PhpDA\Parser\Visitor\Feature\UsedNamespaceCollectorInterface;
use PhpDA\Parser\Visitor\Required\NameResolver;
use PhpParser\Node;

class TagCollector extends AbstractVisitor implements UsedNamespaceCollectorInterface
{
    const ENABLE_OPCODE_SAVE_COMMENT = "Enable opcache.save_comments=1 or zend_optimizerplus.save_comments=1.";

    const ENABLE_OPCODE_LOAD_COMMENT = "Enable opcache.load_comments=1 or zend_optimizerplus.load_comments=1.";

    public function __construct()
    {
        $this->validateZendOptimizer();
        $this->validateZendOpCache();
    }

    /**
     * @codeCoverageIgnore
     * @throws \LogicException
     */
    private function validateZendOptimizer()
    {
        if (extension_loaded('Zend Optimizer+')) {
            if (ini_get('zend_optimizerplus.save_comments') === "0"
                || ini_get('opcache.save_comments') === "0"
            ) {
                throw new \LogicException(self::ENABLE_OPCODE_SAVE_COMMENT);
            }
            if (ini_get('zend_optimizerplus.load_comments') === "0"
                || ini_get('opcache.load_comments') === "0"
            ) {
                throw new \LogicException(self::ENABLE_OPCODE_LOAD_COMMENT);
            }
        }
    }

    /**
     * @codeCoverageIgnore
     * @throws \LogicException
     */
    private function validateZendOpCache()
    {
        if (extension_loaded('Zend OPcache')) {
            if (ini_get('opcache.save_comments') === "0") {
                throw new \LogicException(self::ENABLE_OPCODE_SAVE_COMMENT);
            }
            if (ini_get('opcache.load_comments') === "0") {
                throw new \LogicException(self::ENABLE_OPCODE_LOAD_COMMENT);
            }
        }
    }

    public function leaveNode(Node $node)
    {
        if ($node->hasAttribute(NameResolver::TAG_NAMES_ATTRIBUTE)) {
            $tags = $node->getAttribute(NameResolver::TAG_NAMES_ATTRIBUTE);
            foreach ($tags as $tagName) {
                $name = new Node\Name($tagName);
                $this->collect($name);
            }
        }
    }
}
