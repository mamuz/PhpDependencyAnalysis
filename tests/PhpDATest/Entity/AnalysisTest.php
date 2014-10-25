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

namespace PhpDATest\Entity;

use PhpDA\Entity\Analysis;

class AnalysisTest extends \PHPUnit_Framework_TestCase
{
    /** @var Analysis */
    protected $fixture;

    /** @var \Symfony\Component\Finder\SplFileInfo | \Mockery\MockInterface */
    protected $file;

    protected function setUp()
    {
        $this->file = \Mockery::mock('Symfony\Component\Finder\SplFileInfo');
        $this->fixture = new Analysis($this->file);
    }

    public function testAccessFile()
    {
        $this->assertSame($this->file, $this->fixture->getFile());
    }


    public function testMutateAndAccessParseError()
    {
        $this->assertFalse($this->fixture->hasParseError());
        $this->assertNull($this->fixture->getParseError());

        $error = new \PhpParser\Error('');
        $this->fixture->setParseError($error);

        $this->assertTrue($this->fixture->hasParseError());
        $this->assertSame($error, $this->fixture->getParseError());
    }

    public function testAdtCreation()
    {
        $adt1 = $this->fixture->createAdt();
        $adt2 = $this->fixture->createAdt();

        $this->assertInstanceOf('PhpDA\Entity\Adt', $adt1);
        $this->assertNotSame($adt2, $adt1);
        $this->assertEquals($adt2, $adt1);

        $adts = $this->fixture->getAdts();
        $this->assertSame($adts[0], $adt1);
        $this->assertSame($adts[1], $adt2);
    }
}
