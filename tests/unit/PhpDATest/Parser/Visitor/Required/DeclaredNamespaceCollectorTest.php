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

    public function testNotCollectingByFilteredToNull()
    {
        $namespace = 'Foo\Bar';
        $node = \Mockery::mock('PhpParser\Node\Stmt\ClassLike');
        $node->name = $namespace;
        $node->namespacedName = $namespace;
        $this->adt->shouldReceive('hasDeclaredGlobalNamespace')->once()->andReturn(true);
        $this->nodeNameFilter->shouldReceive('filter')->once()->andReturn(null);
        $this->fixture->leaveNode($node);
    }

    public function testHavingNamespaceException()
    {
        self::expectException('PhpParser\Error');
        $node = \Mockery::mock('PhpParser\Node\Stmt\ClassLike');
        $node->name = true;
        $node->shouldReceive('getLine')->andReturn(12);
        $this->adt->shouldReceive('hasDeclaredGlobalNamespace')->once()->andReturn(false);
        $this->fixture->leaveNode($node);
    }

    public function testCollecting()
    {
        $namespace = 'Foo\Bar';
        $attributes = array('foo' => 'bar');
        $node = \Mockery::mock('PhpParser\Node\Stmt\ClassLike');
        $node->name = $namespace;
        $node->namespacedName = $namespace;
        $node->shouldReceive('getAttributes')->once()->andReturn($attributes);
        $this->nodeNameFilter->shouldReceive('filter')->once()->andReturnUsing(
            function ($nodeName) {
                return $nodeName;
            }
        );
        $this->adt->shouldReceive('hasDeclaredGlobalNamespace')->once()->andReturn(true);
        $this->adt->shouldReceive('setDeclaredNamespace')->once()->andReturnUsing(
            function ($nodeName) use ($namespace) {
                /** @var \PhpParser\Node\Name $nodeName */
                self::assertInstanceOf('PhpParser\Node\Name', $nodeName);
                self::assertSame('bar', $nodeName->getAttribute('foo'));
                self::assertSame($namespace, $nodeName->toString());
            }
        );
        $this->fixture->leaveNode($node);
    }

    public function testNotCollectingAnonymousClass()
    {
        $node = \Mockery::mock('PhpParser\Node\Stmt\ClassLike');
        $node->name = null;
        $this->fixture->leaveNode($node);
    }
}
