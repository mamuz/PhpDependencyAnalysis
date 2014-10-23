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

namespace PhpDATest\Parser;

use PhpDA\Parser\Analyzer;

class AnalyzerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Analyzer */
    protected $fixture;

    /** @var \PhpParser\ParserAbstract | \Mockery\MockInterface */
    protected $parser;

    /** @var \PhpDA\Parser\AdtTraverser | \Mockery\MockInterface */
    protected $adtTraverser;

    /** @var \PhpDA\Parser\NodeTraverser | \Mockery\MockInterface */
    protected $nodeTraverser;

    /** @var \PhpDA\Entity\AnalysisCollection | \Mockery\MockInterface */
    protected $collection;

    /** @var \Symfony\Component\Finder\SplFileInfo | \Mockery\MockInterface */
    protected $file;

    protected function setUp()
    {
        $this->file = \Mockery::mock('Symfony\Component\Finder\SplFileInfo');
        $this->file->shouldReceive('getContents')->andReturn('foo');
        $this->parser = \Mockery::mock('PhpParser\ParserAbstract');
        $this->adtTraverser = \Mockery::mock('PhpDA\Parser\AdtTraverser');
        $this->nodeTraverser = \Mockery::mock('PhpDA\Parser\NodeTraverser');
        $this->collection = \Mockery::mock('PhpDA\Entity\AnalysisCollection');

        $this->nodeTraverser->shouldReceive('setAnalysis');

        $this->fixture = new Analyzer(
            $this->parser,
            $this->adtTraverser,
            $this->nodeTraverser,
            $this->collection
        );
    }

    public function testAccessNodeTraverser()
    {
        $this->assertSame($this->nodeTraverser, $this->fixture->getNodeTraverser());
    }

    public function testGetAnalysisCollection()
    {
        $this->assertSame($this->collection, $this->fixture->getAnalysisCollection());
    }

    public function testAnalyzeWithParseError()
    {
        $testcase = $this;
        $this->parser->shouldReceive('parse')->once()->with('foo')->andThrow('PhpParser\Error');
        $this->collection->shouldReceive('attach')->once()->andReturnUsing(
            function ($analysis) use ($testcase) {
                $testcase->assertInstanceOf('PhpDA\Entity\Analysis', $analysis);
                /** @var \PhpDA\Entity\Analysis $analysis */
                $testcase->assertTrue($analysis->hasParseError());
            }
        );

        $analysis = $this->fixture->analyze($this->file);
        $this->assertInstanceOf('PhpDA\Entity\Analysis', $analysis);
    }

    public function testAnalyze()
    {
        $testcase = $this;
        $stmts = array('foo', 'bar');
        $adts = array('baz', 'faz');
        $this->parser->shouldReceive('parse')->once()->with('foo')->andReturn($stmts);
        $this->adtTraverser->shouldReceive('getAdtStmtsBy')->once()->with($stmts)->andReturn($adts);
        $this->nodeTraverser->shouldReceive('setAdt')->twice()->andReturnUsing(
            function ($adt) use ($testcase) {
                $testcase->assertInstanceOf('PhpDA\Entity\Adt', $adt);
            }
        );
        $this->nodeTraverser->shouldReceive('traverse')->once()->with(array('baz'));
        $this->nodeTraverser->shouldReceive('traverse')->once()->with(array('faz'));
        $this->collection->shouldReceive('attach')->once()->andReturnUsing(
            function ($analysis) use ($testcase) {
                $testcase->assertInstanceOf('PhpDA\Entity\Analysis', $analysis);
                /** @var \PhpDA\Entity\Analysis $analysis */
                $testcase->assertFalse($analysis->hasParseError());
            }
        );

        $analysis = $this->fixture->analyze($this->file);
        $this->assertInstanceOf('PhpDA\Entity\Analysis', $analysis);
    }
}
