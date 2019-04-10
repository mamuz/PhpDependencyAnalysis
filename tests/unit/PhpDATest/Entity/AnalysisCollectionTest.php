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

namespace PhpDATest\Entity;

use PhpDA\Entity\AnalysisCollection;

class AnalysisCollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var AnalysisCollection */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new AnalysisCollection;
    }

    public function testExtendingArrayCollection()
    {
        self::assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $this->fixture);
    }

    public function testAttachingAnalyses()
    {
        $array = $this->fixture->getAll();
        self::assertInstanceOf('ArrayIterator', $array);
        self::assertSame(0, count($array));

        $analysis1 = \Mockery::mock('PhpDA\Entity\Analysis');
        $analysis2 = \Mockery::mock('PhpDA\Entity\Analysis');

        $this->fixture->attach($analysis1);
        $this->fixture->attach($analysis2);

        $array = $this->fixture->getAll();
        self::assertInstanceOf('ArrayIterator', $array);
        self::assertSame(2, count($array));
        self::assertSame($analysis1, $array->offsetGet('0'));
        self::assertSame($analysis2, $array->offsetGet('1'));
    }
}
