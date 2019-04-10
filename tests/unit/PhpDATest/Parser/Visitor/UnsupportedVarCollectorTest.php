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

namespace PhpDATest\Parser\Visitor;

use PhpDA\Parser\Visitor\UnsupportedVarCollector;

class UnsupportedVarCollectorTest extends \PHPUnit_Framework_TestCase
{
    /** @var UnsupportedVarCollector */
    protected $fixture;

    /** @var \PhpDA\Entity\Adt | \Mockery\MockInterface */
    protected $adt;

    /** @var \PhpDA\Parser\Filter\NodeNameInterface | \Mockery\MockInterface */
    protected $nodeNameFilter;

    protected function setUp()
    {
        $this->adt = \Mockery::mock('PhpDA\Entity\Adt');
        $this->nodeNameFilter = \Mockery::mock('PhpDA\Parser\Filter\NodeNameInterface');

        $this->fixture = new UnsupportedVarCollector;
        $this->fixture->setAdt($this->adt);
        $this->fixture->setNodeNameFilter($this->nodeNameFilter);
    }

    public function testNotCollectingByInvalidNode()
    {
        $node = \Mockery::mock('PhpParser\Node');
        $this->fixture->leaveNode($node);
    }

    public function testCollectingByFilteredToNullForNewExpr()
    {
        $node = \Mockery::mock('PhpParser\Node\Expr\New_');
        $node->class = \Mockery::mock('PhpParser\Node\Expr\Variable');
        self::assertCollectingByFilteredToNull($node);
    }

    public function testCollectingByFilteredToNullForVariable()
    {
        $node = \Mockery::mock('PhpParser\Node\Expr\Variable');
        self::assertCollectingByFilteredToNull($node);
    }

    public function testCollectingByFilteredToNullForFuncCall()
    {
        $node = \Mockery::mock('PhpParser\Node\Expr\FuncCall');
        self::assertCollectingByFilteredToNull($node);
    }

    public function testCollectingByFilteredToNullForStaticCall()
    {
        $node = \Mockery::mock('PhpParser\Node\Expr\StaticCall');
        self::assertCollectingByFilteredToNull($node);
    }

    protected function assertCollectingByFilteredToNull($node)
    {
        $node->name = \Mockery::mock('PhpParser\Node\Expr\Variable');
        $this->nodeNameFilter->shouldReceive('filter')->andReturn(null);
        $this->fixture->leaveNode($node);
    }

    public function testCollectingNewExpr()
    {
        $node = \Mockery::mock('PhpParser\Node\Expr\New_');
        $node->class = \Mockery::mock('PhpParser\Node\Expr\Variable');
        self::assertCollecting($node);
    }

    public function testCollectingVariable()
    {
        $node = \Mockery::mock('PhpParser\Node\Expr\Variable');
        self::assertCollecting($node);
    }

    public function testCollectingFuncCall()
    {
        $node = \Mockery::mock('PhpParser\Node\Expr\FuncCall');
        self::assertCollecting($node);
    }

    public function testCollectingStaticCall()
    {
        $node = \Mockery::mock('PhpParser\Node\Expr\StaticCall');
        self::assertCollecting($node);
    }

    protected function assertCollecting(\Mockery\MockInterface $node)
    {
        $attributes = array('foo' => 'bar');
        $node->shouldReceive('getAttributes')->andReturn($attributes);
        $this->nodeNameFilter->shouldReceive('filter')->once()->andReturnUsing(
            function ($object) {
                return $object;
            }
        );
        $this->adt->shouldReceive('addUnsupportedStmt')->once()->andReturnUsing(
            function ($object) {
                /** @var \PhpParser\Node\Name $object */
                self::assertInstanceOf('PhpParser\Node\Name', $object);
                self::assertSame($object->toString(), 'dynamic varname');
            }
        );

        $this->fixture->leaveNode($node);
    }
}
