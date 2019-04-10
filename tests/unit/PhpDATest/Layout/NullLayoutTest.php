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

namespace PhpDATest\Layout;

use PhpDA\Layout\NullLayout;

class NullLayoutTest extends \PHPUnit_Framework_TestCase
{
    /** @var NullLayout */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new NullLayout;
    }

    public function testAccessGraph()
    {
        self::assertSame(array(), $this->fixture->getGraph());
    }

    public function testAccessGroup()
    {
        self::assertSame(array(), $this->fixture->getGroup());
    }

    public function testAccessEdge()
    {
        self::assertSame(array(), $this->fixture->getEdge());
    }

    public function testAccessEdgeExtend()
    {
        self::assertSame(array(), $this->fixture->getEdgeExtend());
    }

    public function testAccessEdgeImplement()
    {
        self::assertSame(array(), $this->fixture->getEdgeImplement());
    }

    public function testAccessEdgeTraitUse()
    {
        self::assertSame(array(), $this->fixture->getEdgeTraitUse());
    }

    public function testAccessEdgeUnsupported()
    {
        self::assertSame(array(), $this->fixture->getEdgeUnsupported());
    }

    public function testAccessEdgeNamespacedString()
    {
        self::assertSame(array(), $this->fixture->getEdgeNamespacedString());
    }

    public function testAccessVertex()
    {
        self::assertSame(array(), $this->fixture->getVertex());
    }

    public function testAccessVertexNamespacedString()
    {
        self::assertSame(array(), $this->fixture->getVertexNamespacedString());
    }

    public function testAccessVertexUnsupported()
    {
        self::assertSame(array(), $this->fixture->getVertexUnsupported());
    }

    public function testAccessEdgeInvalid()
    {
        self::assertSame(array(), $this->fixture->getEdgeInvalid());
    }

    public function testAccessEdgeCycle()
    {
        self::assertSame(array(), $this->fixture->getEdgeCycle());
    }
}
