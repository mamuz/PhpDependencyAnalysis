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

namespace PhpDA\Parser;

use PhpDA\Entity\Analysis;
use PhpDA\Entity\AnalysisCollection;
use PhpParser\Error;
use PhpParser\ParserAbstract;
use Symfony\Component\Finder\SplFileInfo;

class Analyzer implements AnalyzerInterface
{
    /** @var ParserAbstract */
    private $parser;

    /** @var AdtTraverser */
    private $adtTraverser;

    /** @var NodeTraverser */
    private $nodeTraverser;

    /** @var Logger */
    private $logger;

    /** @var AnalysisCollection */
    private $collection;

    /**
     * @param ParserAbstract $parser
     * @param AdtTraverser   $adtTraverser
     * @param NodeTraverser  $nodeTraverser
     * @param Logger         $logger
     */
    public function __construct(
        ParserAbstract $parser,
        AdtTraverser $adtTraverser,
        NodeTraverser $nodeTraverser,
        Logger $logger
    ) {
        $this->parser = $parser;
        $this->adtTraverser = $adtTraverser;
        $this->nodeTraverser = $nodeTraverser;
        $this->logger = $logger;

        $this->collection = new AnalysisCollection;
    }

    public function getNodeTraverser()
    {
        return $this->nodeTraverser;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function analyze(SplFileInfo $file)
    {
        $analysis = new Analysis($file);

        try {
            if ($stmts = $this->parser->parse($file->getContents())) {
                $this->adtTraverser->bindFile($file);
                $adtStmts = $this->adtTraverser->getAdtStmtsBy($stmts);
                foreach ($adtStmts as $node) {
                    $this->nodeTraverser->setAdt($analysis->createAdt());
                    $this->nodeTraverser->traverse(array($node));
                }
            }
        } catch (Error $error) {
            $this->logger->error($error->getMessage(), array($file));
        }

        $this->getAnalysisCollection()->attach($analysis);

        return $analysis;
    }

    public function getAnalysisCollection()
    {
        return $this->collection;
    }
}
