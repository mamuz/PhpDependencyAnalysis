<?php

namespace PhpDA\Parser;

use PhpDA\Entity\Analysis;
use PhpDA\Entity\AnalysisAwareInterface;
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

    /** @var AnalysisCollection */
    private $collection;

    /**
     * @param ParserAbstract         $parser
     * @param NodeTraverserInterface $traveser
     */
    public function __construct(ParserAbstract $parser, NodeTraverserInterface $traveser)
    {
        $this->parser = $parser;
        $this->traverser = $traveser;
        $this->collection = new AnalysisCollection;
    }

    public function analyze(SplFileInfo $file)
    {
        $analysis = new Analysis;

        if ($this->traverser instanceof AnalysisAwareInterface) {
            $this->traverser->setAnalysis($analysis);
        }

        try {
            $stmts = $this->parser->parse($file->getContents());
            $this->traverser->traverse($stmts);
        } catch (Error $error) {
            $analysis->setParseError($error);
        }

        $this->collection->attach($analysis, $file->getRealPath());
        return $analysis;
    }

    public function getAnalysisCollection()
    {
        return $this->collection;
    }
}
