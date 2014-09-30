<?php

namespace PhpDA\Service;

use PhpDA\Command\Analyze;
use PhpDA\Parser\Analyzer;
use PhpDA\Parser\Visitor\Mapper;
use PhpDA\Writer\Adapter;
use PhpDA\Writer\Loader;
use PhpParser\Lexer\Emulative;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use Symfony\Component\Console\Application;
use Symfony\Component\Finder\Finder;

class ApplicationFactory
{
    /**
     * @return Application
     */
    public function create()
    {
        $app = new Application;
        $app->add($this->createAnalyzeCommand());

        return $app;
    }

    /**
     * @return Analyze
     */
    protected function createAnalyzeCommand()
    {
        $command = new Analyze;
        $command->setFinder($this->createFinder());
        $command->setAnalyzer($this->createAnalyzer());
        $command->setWriteAdapter($this->createWriteAdapter());

        return $command;
    }

    /**
     * @return Finder
     */
    protected function createFinder()
    {
        return new Finder;
    }

    /**
     * @return Analyzer
     */
    protected function createAnalyzer()
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
        $traverser->addVisitor(new Mapper);

        return $traverser;
    }

    /**
     * @return Adapter
     */
    protected function createWriteAdapter()
    {
        return new Adapter(new Loader);
    }
}
