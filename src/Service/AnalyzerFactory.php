<?php

namespace PhpDA\Service;

use Fhaculty\Graph\Graph;
use PhpDA\Entity\AnalysisCollection;
use PhpDA\Parser\Analyzer;
use PhpDA\Parser\NodeTraverser;
use PhpDA\Plugin\Loader;
use PhpParser\Lexer\Emulative;
use PhpParser\Parser;

class AnalyzerFactory implements FactoryInterface
{
    /**
     * @return Analyzer
     */
    public function create()
    {
        return new Analyzer(
            $this->createParser(),
            $this->createTraverser(),
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
     * @return NodeTraverser
     */
    protected function createTraverser()
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
