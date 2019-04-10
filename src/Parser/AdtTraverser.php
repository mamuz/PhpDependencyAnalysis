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

use PhpDA\Parser\Visitor\Required\AdtCollector;
use PhpDA\Parser\Visitor\Required\NameResolver;
use PhpParser\Node;
use Symfony\Component\Finder\SplFileInfo;

class AdtTraverser extends \PhpParser\NodeTraverser
{
    /** @var AdtCollector */
    private $adtCollector;

    /** @var NameResolver */
    private $nameResolver;

    /**
     * @param AdtCollector $adtCollector
     */
    public function bindAdtCollector(AdtCollector $adtCollector)
    {
        $this->adtCollector = $adtCollector;
        $this->addVisitor($adtCollector);
    }

    /**
     * @param NameResolver $nameResolver
     */
    public function bindNameResolver(NameResolver $nameResolver)
    {
        $this->nameResolver = $nameResolver;
        $this->addVisitor($nameResolver);
    }

    /**
     * @param SplFileInfo $file
     * @throws \DomainException
     */
    public function bindFile(SplFileInfo $file)
    {
        if (!$this->nameResolver instanceof NameResolver) {
            throw new \DomainException('NameResolver has not been set');
        }

        $this->nameResolver->setFile($file);
    }

    /**
     * @param Node[] $nodes Array of nodes
     * @return array
     */
    public function getAdtStmtsBy(array $nodes)
    {
        $this->adtCollector->flush();
        $this->traverse($nodes);

        return $this->adtCollector->getStmts();
    }
}
