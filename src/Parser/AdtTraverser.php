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

namespace PhpDA\Parser;

use PhpDA\Parser\Visitor\Required\AdtCollector;
use PhpParser\Node;

class AdtTraverser extends \PhpParser\NodeTraverser
{
    /** @var AdtCollector */
    private $adtCollector;

    /**
     * @param AdtCollector $adtCollector
     */
    public function setAdtCollector(AdtCollector $adtCollector)
    {
        $this->adtCollector = $adtCollector;
        $this->addVisitor($adtCollector);
    }

    /**
     * @param Node[] $nodes Array of nodes
     * @return array
     */
    public function getAdtStmtsBy(array $nodes)
    {
        $this->adtCollector->flush();
        parent::traverse($nodes);

        return $this->adtCollector->getStmts();
    }
}
