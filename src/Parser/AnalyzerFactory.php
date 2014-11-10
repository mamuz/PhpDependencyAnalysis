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

use Fhaculty\Graph\Graph;
use PhpDA\Entity\AnalysisCollection;
use PhpDA\Parser\Visitor\Required\AdtCollector;
use PhpDA\Parser\Visitor\Required\NameResolver;
use PhpDA\Plugin\FactoryInterface;
use PhpDA\Plugin\Loader;
use PhpParser\Lexer\Emulative;
use PhpParser\Parser;

/**
 * @SuppressWarnings("PMD.CouplingBetweenObjects")
 */
class AnalyzerFactory implements FactoryInterface
{
    /**
     * @return Analyzer
     */
    public function create()
    {
        return new Analyzer(
            $this->createParser(),
            $this->createAdtTraverser(),
            $this->createNodeTraverser(),
            $this->createCollection()
        );
    }

    /**
     * @return Parser
     */
    protected function createParser()
    {
        return new Parser(new Emulative);
    }

    /**
     * @return AdtTraverser
     */
    protected function createAdtTraverser()
    {
        $traverser = new AdtTraverser;
        $traverser->addVisitor(new NameResolver);
        $traverser->setAdtCollector(new AdtCollector);

        return $traverser;
    }

    /**
     * @return NodeTraverser
     */
    protected function createNodeTraverser()
    {
        $traverser = new NodeTraverser;
        $traverser->setVisitorLoader(new Loader);

        return $traverser;
    }

    /**
     * @return AnalysisCollection
     */
    protected function createCollection()
    {
        return new AnalysisCollection(new Graph);
    }
}
