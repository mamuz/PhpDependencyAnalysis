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

namespace PhpDATest\Parser;

use PhpDA\Parser\Analyzer;

class AnalyzerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Analyzer */
    protected $fixture;

    /** @var \PhpParser\Parser | \Mockery\MockInterface */
    protected $parser;

    /** @var \PhpDA\Parser\AdtTraverser | \Mockery\MockInterface */
    protected $adtTraverser;

    /** @var \PhpDA\Parser\NodeTraverser | \Mockery\MockInterface */
    protected $nodeTraverser;

    /** @var \Symfony\Component\Finder\SplFileInfo | \Mockery\MockInterface */
    protected $file;

    /** @var \PhpDA\Parser\Logger | \Mockery\MockInterface */
    protected $logger;

    protected function setUp()
    {
        $this->logger = \Mockery::mock('PhpDA\Parser\Logger');
        $this->file = \Mockery::mock('Symfony\Component\Finder\SplFileInfo');
        $this->file->shouldReceive('getContents')->andReturn('foo');
        $this->parser = \Mockery::mock('PhpParser\Parser');
        $this->adtTraverser = \Mockery::mock('PhpDA\Parser\AdtTraverser');
        $this->nodeTraverser = \Mockery::mock('PhpDA\Parser\NodeTraverser');

        $this->nodeTraverser->shouldReceive('setAnalysis');

        $this->fixture = new Analyzer(
            $this->parser,
            $this->adtTraverser,
            $this->nodeTraverser,
            $this->logger
        );
    }

    public function testAccessNodeTraverser()
    {
        self::assertSame($this->nodeTraverser, $this->fixture->getNodeTraverser());
    }

    public function testAccessLogger()
    {
        self::assertSame($this->logger, $this->fixture->getLogger());
    }

    public function testAnalyzeWithParseError()
    {
        $exception = new \PhpParser\Error('errormessage');
        $this->parser->shouldReceive('parse')->once()->with('foo')->andThrow($exception);
        $this->logger->shouldReceive('error')->once()->with('errormessage on unknown line', array($this->file));

        $analysis = $this->fixture->analyze($this->file);
        self::assertInstanceOf('PhpDA\Entity\Analysis', $analysis);
    }

    public function testAnalyze()
    {
        $stmts = array('foo', 'bar');
        $adts = array('baz', 'faz');
        $this->parser->shouldReceive('parse')->once()->with('foo')->andReturn($stmts);
        $this->adtTraverser->shouldReceive('bindFile')->once()->with($this->file);
        $this->adtTraverser->shouldReceive('getAdtStmtsBy')->once()->with($stmts)->andReturn($adts);
        $this->nodeTraverser->shouldReceive('setAdt')->twice()->andReturnUsing(
            function ($adt) {
                self::assertInstanceOf('PhpDA\Entity\Adt', $adt);
            }
        );
        $this->nodeTraverser->shouldReceive('traverse')->once()->with(array('baz'));
        $this->nodeTraverser->shouldReceive('traverse')->once()->with(array('faz'));

        $analysis = $this->fixture->analyze($this->file);
        self::assertInstanceOf('PhpDA\Entity\Analysis', $analysis);

        $collection = $this->fixture->getAnalysisCollection();
        self::assertSame($analysis, $collection->offsetGet('0'));
    }
}
