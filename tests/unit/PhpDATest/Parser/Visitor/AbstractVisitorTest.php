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

class AbstractVisitorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PhpDA\Parser\Visitor\AbstractVisitor */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = $this->getMockForAbstractClass('PhpDA\Parser\Visitor\AbstractVisitor');
    }

    public function testExtendingNodeVisitor()
    {
        self::assertInstanceOf('PhpParser\NodeVisitorAbstract', $this->fixture);
    }

    public function testMutateAndAccessNodeNameFilter()
    {
        $filter = \Mockery::mock('PhpDA\Parser\Filter\NodeNameInterface');
        $this->fixture->setNodeNameFilter($filter);
        self::assertSame($filter, $this->fixture->getNodeNameFilter());
    }

    public function testMutateAndAccessNodeNameFilterInitial()
    {
        self::assertInstanceOf('PhpDA\Parser\Filter\NodeName', $this->fixture->getNodeNameFilter());
    }

    public function testMutateAndAccessAdt()
    {
        $adt = \Mockery::mock('PhpDA\Entity\Adt');
        $this->fixture->setAdt($adt);
        self::assertSame($adt, $this->fixture->getAdt());
    }

    public function testNullPointerExceptionOnAdtAccess()
    {
        self::expectException('DomainException');
        $this->fixture->getAdt();
    }

    public function testAccessNodeNameFilter()
    {
        self::assertInstanceOf(
            'PhpDA\Parser\Filter\NodeNameInterface',
            $this->fixture->getNodeNameFilter()
        );
    }

    public function testDelegatingOptionsToNodeNameFilter()
    {
        $options = array('foo');
        $filter = \Mockery::mock('PhpDA\Parser\Filter\NodeNameInterface');
        $filter->shouldReceive('setOptions')->once()->with($options);
        $this->fixture->setNodeNameFilter($filter);

        $this->fixture->setOptions($options);
    }
}
