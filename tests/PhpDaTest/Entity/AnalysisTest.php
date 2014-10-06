<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2014 Marco Muths
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace PhpDaTest\Entity;

use PhpDA\Entity\Analysis;

class AnalysisTest extends \PHPUnit_Framework_TestCase
{
    /** @var Analysis */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new Analysis;
    }

    public function testMutateAndAccessParseError()
    {
        $this->assertFalse($this->fixture->hasParseError());
        $this->assertNull($this->fixture->getParseError());

        $error = \Mockery::mock('PhpParser\Error');
        $this->fixture->setParseError($error);

        $this->assertTrue($this->fixture->hasParseError());
        $this->assertSame($error, $this->fixture->getParseError());
    }
}
