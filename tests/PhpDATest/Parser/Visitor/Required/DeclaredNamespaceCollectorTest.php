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

namespace PhpDATest\Parser\Visitor\Required;

use PhpDA\Parser\Visitor\Required\DeclaredNamespaceCollector;

class DeclaredNamespaceCollectorTest extends \PHPUnit_Framework_TestCase
{
    /** @var DeclaredNamespaceCollector */
    protected $fixture;

    /** @var \PhpDA\Entity\Adt | \Mockery\MockInterface */
    protected $adt;

    /** @var \PhpDA\Parser\Filter\NodeNameInterface | \Mockery\MockInterface */
    protected $nodeNameFilter;

    protected function setUp()
    {
        $this->adt = \Mockery::mock('PhpDA\Entity\Adt');
        $this->nodeNameFilter = \Mockery::mock('PhpDA\Parser\Filter\NodeNameInterface');

        $this->fixture = new DeclaredNamespaceCollector;
        $this->fixture->setAdt($this->adt);
        $this->fixture->setNodeNameFilter($this->nodeNameFilter);
    }

    public function testNotCollectingByInvalidNode()
    {
        $name = \Mockery::mock('PhpParser\Node');
        $this->fixture->leaveNode($name);
    }

    public function testNotCollectingByFilteredToNullForClassNode()
    {
        $node = \Mockery::mock('PhpParser\Node\Stmt\Class_');
        $this->assertFilteredToNullBy($node);
    }

    public function testNotCollectingByFilteredToNullForTraitNode()
    {
        $node = \Mockery::mock('PhpParser\Node\Stmt\Trait_');
        $this->assertFilteredToNullBy($node);
    }

    public function testNotCollectingByFilteredToNullForInterfaceNode()
    {
        $node = \Mockery::mock('PhpParser\Node\Stmt\Interface_');
        $this->assertFilteredToNullBy($node);
    }

    protected function assertFilteredToNullBy(\Mockery\MockInterface $node)
    {
        $namespace = 'Foo\Bar';
        $iterator = \Mockery::mock('ArrayIterator');
        $iterator->shouldReceive('offsetExists')->once()->with('namespacedName')->andReturn(true);
        $iterator->shouldReceive('offsetGet')->once()->with('namespacedName')->andReturn($namespace);
        $node->shouldReceive('getIterator')->once()->andReturn($iterator);
        $this->adt->shouldReceive('hasDeclaredGlobalNamespace')->once()->andReturn(true);
        $this->nodeNameFilter->shouldReceive('filter')->once()->andReturn(null);
        $this->fixture->leaveNode($node);
    }

    public function testNotCollectingByOffsetNotExistByForClassNode()
    {
        $node = \Mockery::mock('PhpParser\Node\Stmt\Class_');
        $this->assertFilteredToNullBy($node);
    }

    public function testNotCollectingByOffsetNotExistByForTraitNode()
    {
        $node = \Mockery::mock('PhpParser\Node\Stmt\Trait_');
        $this->assertFilteredToNullBy($node);
    }

    public function testNotCollectingByOffsetNotExistByForInterfaceNode()
    {
        $node = \Mockery::mock('PhpParser\Node\Stmt\Interface_');
        $this->assertFilteredToNullBy($node);
    }

    protected function assertOffsetNotExistBy(\Mockery\MockInterface $node)
    {
        $iterator = \Mockery::mock('ArrayIterator');
        $iterator->shouldReceive('offsetExists')->once()->with('namespacedName')->andReturn(false);
        $node->shouldReceive('getIterator')->once()->andReturn($iterator);
        $this->fixture->leaveNode($node);
    }


    public function testHavingNamespaceExceptionForClassNode()
    {
        $node = \Mockery::mock('PhpParser\Node\Stmt\Class_');
        $this->assertExceptionBy($node);
    }

    public function testHavingNamespaceExceptionForTraitNode()
    {
        $node = \Mockery::mock('PhpParser\Node\Stmt\Trait_');
        $this->assertExceptionBy($node);
    }

    public function testHavingNamespaceExceptionForInterfaceNode()
    {
        $node = \Mockery::mock('PhpParser\Node\Stmt\Interface_');
        $this->assertExceptionBy($node);
    }

    protected function assertExceptionBy(\Mockery\MockInterface $node)
    {
        $this->setExpectedException('PhpParser\Error');
        $iterator = \Mockery::mock('ArrayIterator');
        $iterator->shouldReceive('offsetExists')->once()->with('namespacedName')->andReturn(true);
        $node->shouldReceive('getIterator')->once()->andReturn($iterator);
        $node->shouldReceive('getLine')->andReturn(12);
        $this->adt->shouldReceive('hasDeclaredGlobalNamespace')->once()->andReturn(false);
        $this->fixture->leaveNode($node);
    }

    public function testCollectingClass()
    {
        $node = \Mockery::mock('PhpParser\Node\Stmt\Class_');
        $this->assertCollectingBy($node);
    }

    public function testCollectingTrait()
    {
        $node = \Mockery::mock('PhpParser\Node\Stmt\Trait_');
        $this->assertCollectingBy($node);
    }

    public function testCollectingInterface()
    {
        $node = \Mockery::mock('PhpParser\Node\Stmt\Interface_');
        $this->assertCollectingBy($node);
    }

    protected function assertCollectingBy(\Mockery\MockInterface $node)
    {
        $testcase = $this;
        $namespace = 'Foo\Bar';
        $iterator = \Mockery::mock('ArrayIterator');
        $iterator->shouldReceive('offsetExists')->once()->with('namespacedName')->andReturn(true);
        $iterator->shouldReceive('offsetGet')->once()->with('namespacedName')->andReturn($namespace);
        $attributes = array('foo' => 'bar');
        $node->shouldReceive('getAttributes')->once()->andReturn($attributes);
        $node->shouldReceive('getIterator')->once()->andReturn($iterator);
        $this->nodeNameFilter->shouldReceive('filter')->once()->andReturnUsing(
            function ($nodeName) {
                return $nodeName;
            }
        );
        $this->adt->shouldReceive('hasDeclaredGlobalNamespace')->once()->andReturn(true);
        $this->adt->shouldReceive('setDeclaredNamespace')->once()->andReturnUsing(
            function ($nodeName) use ($namespace, $testcase) {
                /** @var \PhpParser\Node\Name $nodeName */
                $testcase->assertInstanceOf('PhpParser\Node\Name', $nodeName);
                $testcase->assertSame('bar', $nodeName->getAttribute('foo'));
                $testcase->assertSame($namespace, $nodeName->toString());
            }
        );

        $this->fixture->leaveNode($node);
    }
}
