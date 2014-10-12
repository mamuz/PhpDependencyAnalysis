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
        $this->assertInstanceOf('PhpParser\NodeVisitorAbstract', $this->fixture);
    }

    public function testMutateAndAccessAnalysis()
    {
        $this->assertNull($this->fixture->getAnalysis());
        $analysis = \Mockery::mock('PhpDA\Entity\Analysis');
        $this->fixture->setAnalysis($analysis);
        $this->assertSame($analysis, $this->fixture->getAnalysis());
    }
}
