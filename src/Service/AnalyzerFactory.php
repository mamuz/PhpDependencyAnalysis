<?php

namespace PhpDA\Service;

use PhpDA\Parser\Analyzer;
use PhpDA\Parser\NodeTraverser;
use PhpDA\Parser\Visitor\IncludeCollector;
use PhpDA\Parser\Visitor\IocContainerAccessorCollector;
use PhpDA\Parser\Visitor\NamespaceCollector;
use PhpDA\Parser\Visitor\NamespacedStringCollector;
use PhpDA\Parser\Visitor\ShellExecCollector;
use PhpDA\Parser\Visitor\SuperglobalCollector;
use PhpDA\Parser\Visitor\UnsupportedEvalCollector;
use PhpDA\Parser\Visitor\UnsupportedFuncCollector;
use PhpDA\Parser\Visitor\UnsupportedGlobalCollector;
use PhpDA\Parser\Visitor\UnsupportedVarCollector;
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
        $traverser->addVisitor(new NamespaceCollector);
        $traverser->addVisitor(new SuperglobalCollector);
        $traverser->addVisitor(new IncludeCollector);
        $traverser->addVisitor(new ShellExecCollector);
        $traverser->addVisitor(new UnsupportedEvalCollector);
        $traverser->addVisitor(new UnsupportedFuncCollector);
        $traverser->addVisitor(new UnsupportedVarCollector);
        $traverser->addVisitor(new UnsupportedGlobalCollector);
        $traverser->addVisitor(new NamespacedStringCollector);
        $traverser->addVisitor(new IocContainerAccessorCollector);

        return $traverser;
    }
}
