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

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag\ReturnTag;
use PhpParser\Node;

class AnnotationCollector extends AbstractVisitor
{
    /** @var array */
    private $ignoredTypes = array(
        'void',
        'null',
        'bool',
        'boolean',
        'string',
        'array',
        'object',
        'resource',
        'integer',
        'int',
        'float',
        'double',
        'real',
        'binary',
        'callable',
        '$this',
        'this',
        'self'
    );

    public function leaveNode(Node $node)
    {
        if ($doc = $node->getDocComment()) {
            var_dump($doc->getText());
            $docBlock = new DocBlock($doc->getText());
            $tags = $docBlock->getTags();
            foreach ($tags as $tag) {
                if ($tag instanceof ReturnTag) {
                    $types = $tag->getTypes();
                    foreach ($types as $type) {
                        if (strpos($type, '\\') === 0) {
                            $type = rtrim($type, '[]');
                            $type = trim($type, '\\');
                            var_dump($type);
                            $name = new Node\Name($type);
                            $this->exchange($node, $name);
                            $this->addUsedNamespace($name);
                        }
                    }
                }
            }
        }
    }
}
