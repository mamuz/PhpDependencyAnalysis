<?php

namespace PhpDA\Service;

use PhpDA\Parser\Analyzer;
use PhpDA\Parser\NodeTraverser;
use PhpDA\Parser\Visitor\NodeClass;
use PhpParser\Lexer\Emulative;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;

class AnalyzerFactory implements FactoryInterface
{
    /**
     * @return Analyzer
     */
    public function create()
    {
        return new Analyzer($this->createParser(), $this->createTraverser());
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
        $traverser->addVisitor(new NameResolver);
        $traverser->addVisitor(new NodeClass);

        return $traverser;
    }
}
