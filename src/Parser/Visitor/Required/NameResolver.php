<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 Marco Muths
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

use phpDocumentor\Reflection\DocBlock;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\NodeVisitor\NameResolver as PhpParserNameResolver;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @SuppressWarnings("PMD.CouplingBetweenObjects")
 */
class NameResolver extends PhpParserNameResolver implements LoggerAwareInterface
{
    const TAG_NAMES_ATTRIBUTE = '__tagNames';

    /** @var LoggerInterface */
    private $logger;

    /** @var SplFileInfo */
    private $file;

    /** @var array */
    private $ignoredMethodArgumentTypes = array(
        'bool', 'boolean',
        'int', 'integer',
        'string', 'binary', 'array',
        'object', 'resource',
        'mixed', 'null',
        'float', 'double',
        'this', 'self', 'parent', 'static',
        'true', 'false',
        '=', '|'
    );

    public function __construct()
    {
        $this->logger = new NullLogger;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param SplFileInfo $file
     */
    public function setFile(SplFileInfo $file)
    {
        $this->file = $file;
    }

    public function enterNode(Node $node)
    {
        parent::enterNode($node);

        try {
            if ($doc = $node->getDocComment()) {
                $docBlock = new DocBlock($doc->getText());
                if ($tagNames = $this->collectTagNamesBy($docBlock->getTags())) {
                    $node->setAttribute(self::TAG_NAMES_ATTRIBUTE, $tagNames);
                }
            }
        } catch (\Exception $e) {
            $parseError = new Error($e->getMessage(), $node->getLine());
            $this->logger->warning($parseError->getMessage(), array($this->file));
        }
    }

    /**
     * @param DocBlock\Tag[] $docTags
     * @return array
     */
    private function collectTagNamesBy(array $docTags)
    {
        $tagNames = array();
        foreach ($docTags as $tag) {
            if ($tag instanceof DocBlock\Tag\ReturnTag) {
                $types = $tag->getTypes();
                if ($tag instanceof DocBlock\Tag\MethodTag) {
                    $types = array_merge($types, $this->getTypesBy($tag));
                }
                foreach ($types as $type) {
                    if ($resolvedString = $this->resolve($type, $tag)) {
                        $tagNames[$resolvedString] = $resolvedString;
                    }
                }
            }
        }

        return $tagNames;
    }

    /**
     * @param DocBlock\Tag\MethodTag $tag
     * @return array
     */
    private function getTypesBy(DocBlock\Tag\MethodTag $tag)
    {
        $types = array();
        $args = $tag->getArguments();

        foreach ($args as $strings) {
            if (count($strings) > 1) {
                foreach ($strings as $string) {
                    $string = trim($string);
                    if (strpos($string, '$') === false
                        && !in_array(strtolower($string), $this->ignoredMethodArgumentTypes)
                    ) {
                        $types[] = '\\' . trim($string, '\\');
                    }
                }
            }
        }

        return $types;
    }

    /**
     * @param string       $type
     * @param DocBlock\Tag $tag
     * @return string|null
     */
    private function resolve($type, DocBlock\Tag $tag)
    {
        if (strpos($type, '\\') === 0) {
            $type = rtrim($type, '[]');
            $tagName = new Node\Name(trim($type, '\\'));
            if (strpos($tag->getContent(), $type) === false) {
                $tagName = $this->resolveClassName($tagName);
            }
            return $tagName->toString();
        }

        return null;
    }
}
