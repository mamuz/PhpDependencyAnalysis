<?php

namespace PhpDA\Parser;

use PhpDA\Entity\AnalysisCollection;
use PhpParser\Error;
use PhpParser\NodeTraverserInterface;
use PhpParser\ParserAbstract;
use Symfony\Component\Finder\SplFileInfo;

class Analyzer implements AnalyzerInterface
{
    /** @var ParserAbstract */
    private $parser;

    /** @var NodeTraverserInterface */
    private $traverser;

    public function __construct(ParserAbstract $parser, NodeTraverserInterface $traveser)
    {
        $this->parser = $parser;
        $this->traverser = $traveser;
    }

    public function analyze(SplFileInfo $file)
    {
        try {
            $stmts = $this->parser->parse($file->getContents());
            $this->traverser->traverse($stmts);
        } catch (Error $e) {
            // @todo
        }
    }

    public function getAnalysisCollection()
    {
        // @todo
        return new AnalysisCollection;
    }
}
