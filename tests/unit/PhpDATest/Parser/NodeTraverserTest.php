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

use PhpDA\Parser\NodeTraverser;

class NodeTraverserTest extends \PHPUnit_Framework_TestCase
{
    /** @var NodeTraverser */
    protected $fixture;

    /** @var \PhpDA\Plugin\LoaderInterface | \Mockery\MockInterface */
    protected $visitorLoader;

    /** @var \PhpParser\NodeVisitor | \Mockery\MockInterface */
    protected $visitor;

    /** @var array */
    protected $requiredVisitors = array(
        'PhpDA\Parser\Visitor\Required\DeclaredNamespaceCollector',
        'PhpDA\Parser\Visitor\Required\UsedNamespaceCollector',
    );

    protected function setUp()
    {
        $this->visitor = \Mockery::mock('PhpParser\NodeVisitor');
        $this->visitorLoader = \Mockery::mock('PhpDA\Plugin\LoaderInterface');
        foreach ($this->requiredVisitors as $fqcn) {
            $this->visitorLoader->shouldReceive('get')->with($fqcn, null)->andReturn($this->visitor);
        }

        $this->fixture = new NodeTraverser;
        $this->fixture->setRequiredVisitors($this->requiredVisitors);
    }

    public function testAccessRequiredVisitors()
    {
        self::assertSame($this->requiredVisitors, $this->fixture->getRequiredVisitors());
    }

    public function testMutateAndAccessVisitorLoader()
    {
        $this->fixture->setVisitorLoader($this->visitorLoader);
        self::assertSame($this->visitorLoader, $this->fixture->getVisitorLoader());
    }

    public function testNullPointerExceptionForAccessVisitorLoader()
    {
        self::expectException('DomainException');
        $this->fixture->getVisitorLoader();
    }

    public function testBindingVisitors()
    {
        $visitors = array(
            'foo',
            '\\PhpDA\Parser\Visitor\Required\UsedNamespaceCollector\\',
            '\\bar\baz',
            'baz\baz\\',
            'PhpDA\Parser\Visitor\Required\DeclaredNamespaceCollector',
        );

        $this->visitorLoader->shouldReceive('get')->with('foo', null)->andReturn($this->visitor);
        $this->visitorLoader->shouldReceive('get')->with('bar\baz', null)->andReturn($this->visitor);
        $this->visitorLoader->shouldReceive('get')->with('baz\baz', null)->andReturn($this->visitor);
        $this->fixture->setVisitorLoader($this->visitorLoader);

        $this->fixture->bindVisitors($visitors);
    }

    public function testBindingVisitorsWithOptions()
    {
        $visitors = array(
            'foo',
            'bar\baz',
            'baz',
        );
        $options = array(
            'foo\\'       => array('foo'),
            '\\bar\baz\\' => 234,
        );

        $this->visitorLoader->shouldReceive('get')->with('foo', array('foo'))->andReturn($this->visitor);
        $this->visitorLoader->shouldReceive('get')->with('bar\baz', array(234))->andReturn($this->visitor);
        $this->visitorLoader->shouldReceive('get')->with('baz', null)->andReturn($this->visitor);
        $this->fixture->setVisitorLoader($this->visitorLoader);

        $this->fixture->bindVisitors($visitors, $options);
    }

    public function testBindingInvalidVisitor()
    {
        self::expectException('RuntimeException');

        $visitors = array('foo',);

        $this->visitorLoader->shouldReceive('get')->with('foo', null)->andReturn(false);
        $this->fixture->setVisitorLoader($this->visitorLoader);

        $this->fixture->bindVisitors($visitors);
    }

    public function testMutateAndAccessAdt()
    {
        self::assertNull($this->fixture->getAdt());
        self::assertFalse($this->fixture->hasAdt());
        $adt = \Mockery::mock('PhpDA\Entity\Adt');
        $this->fixture->setAdt($adt);
        self::assertTrue($this->fixture->hasAdt());
        self::assertSame($adt, $this->fixture->getAdt());
    }

    public function testTraversingWithAdtAwareness()
    {
        $adt = \Mockery::mock('PhpDA\Entity\Adt');
        $this->fixture->setAdt($adt);

        $visitors = array('foo');
        $visitor = \Mockery::mock('PhpDA\Parser\Visitor\AbstractVisitor');
        $visitor->shouldReceive('setAdt')->once()->with($adt);
        $this->visitorLoader->shouldReceive('get')->with('foo', null)->andReturn($visitor);
        $this->fixture->setVisitorLoader($this->visitorLoader);
        $this->fixture->bindVisitors($visitors);

        $this->visitor->shouldIgnoreMissing();
        $visitor->shouldIgnoreMissing();

        $nodes = array('foo', 'bar');

        self::assertSame($nodes, $this->fixture->traverse($nodes));
    }

    public function testTraversingWithoutAdtAwareness()
    {
        $visitors = array('foo');
        $visitor = \Mockery::mock('PhpDA\Parser\Visitor\AbstractVisitor');
        $this->visitorLoader->shouldReceive('get')->with('foo', null)->andReturn($visitor);
        $this->fixture->setVisitorLoader($this->visitorLoader);
        $this->fixture->bindVisitors($visitors);

        $this->visitor->shouldIgnoreMissing();
        $visitor->shouldIgnoreMissing();

        $nodes = array('foo', 'bar');

        self::assertSame($nodes, $this->fixture->traverse($nodes));
    }
}
