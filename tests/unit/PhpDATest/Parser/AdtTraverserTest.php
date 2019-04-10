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

use PhpDA\Parser\AdtTraverser;

class AdtTraverserTest extends \PHPUnit_Framework_TestCase
{
    /** @var AdtTraverser */
    protected $fixture;

    /** @var \PhpDA\Parser\Visitor\Required\AdtCollector | \Mockery\MockInterface */
    protected $adtCollector;

    /** @var \PhpDA\Parser\Visitor\Required\NameResolver | \Mockery\MockInterface */
    protected $nameResolver;

    /** @var \Symfony\Component\Finder\SplFileInfo | \Mockery\MockInterface */
    protected $file;

    protected function setUp()
    {
        $this->adtCollector = \Mockery::mock('PhpDA\Parser\Visitor\Required\AdtCollector');
        $this->adtCollector->shouldIgnoreMissing();
        $this->nameResolver = \Mockery::mock('PhpDA\Parser\Visitor\Required\NameResolver');
        $this->nameResolver->shouldIgnoreMissing();
        $this->file = \Mockery::mock('Symfony\Component\Finder\SplFileInfo');
        $this->file->shouldIgnoreMissing();

        $this->fixture = new AdtTraverser;
    }

    public function testTraversing()
    {
        $stmtNode = \Mockery::mock('PhpParser\Node');
        $this->adtCollector->shouldReceive('flush')->once();
        $this->adtCollector->shouldReceive('getStmts')->once()->andReturn(array(array($stmtNode)));
        $this->fixture->bindAdtCollector($this->adtCollector);

        $this->nameResolver->shouldReceive('setFile')->once()->with($this->file);
        $this->fixture->bindNameResolver($this->nameResolver);
        $this->fixture->bindFile($this->file);

        $nodes = array('foo', 'bar');
        $stmts = $this->fixture->getAdtStmtsBy($nodes);

        foreach ($stmts as $nodes) {
            foreach ($nodes as $node) {
                self::assertSame($stmtNode, $node);
            }
        }
    }

    public function testThrowExceptionForBindingFileWithoutNameResolver()
    {
        self::expectException('DomainException');
        $this->fixture->bindFile($this->file);
    }
}
