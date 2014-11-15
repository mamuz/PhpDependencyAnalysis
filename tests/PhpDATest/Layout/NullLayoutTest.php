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

    public function testAccessEdge()
    {
        $this->assertSame(array(), $this->fixture->getEdge());
    }

    public function testAccessEdgeExtend()
    {
        $this->assertSame(array(), $this->fixture->getEdgeExtend());
    }

    public function testAccessEdgeImplement()
    {
        $this->assertSame(array(), $this->fixture->getEdgeImplement());
    }

    public function testAccessEdgeTraitUse()
    {
        $this->assertSame(array(), $this->fixture->getEdgeTraitUse());
    }

    public function testAccessEdgeUnsupported()
    {
        $this->assertSame(array(), $this->fixture->getEdgeUnsupported());
    }

    public function testAccessEdgeNamespacedString()
    {
        $this->assertSame(array(), $this->fixture->getEdgeNamespacedString());
    }

    public function testAccessVertex()
    {
        $this->assertSame(array(), $this->fixture->getVertex());
    }

    public function testAccessVertexNamespacedString()
    {
        $this->assertSame(array(), $this->fixture->getVertexNamespacedString());
    }

    public function testAccessVertexUnsupported()
    {
        $this->assertSame(array(), $this->fixture->getVertexUnsupported());
    }
}
