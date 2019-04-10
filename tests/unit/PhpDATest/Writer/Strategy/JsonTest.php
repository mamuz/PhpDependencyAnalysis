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

namespace PhpDATest\Writer\Strategy;

use PhpDA\Writer\Strategy\Json;

class JsonTest extends \PHPUnit_Framework_TestCase
{
    /** @var Json */
    protected $fixture;

    /** @var \PhpDA\Writer\Extractor\ExtractionInterface | \Mockery\MockInterface */
    protected $extractor;

    protected function setUp()
    {
        $this->extractor = \Mockery::mock('PhpDA\Writer\Extractor\ExtractionInterface');
        $this->fixture = new Json;
    }

    public function testMutateAndAccessExtractor()
    {
        self::assertInstanceOf('PhpDA\Writer\Extractor\Graph', $this->fixture->getExtractor());

        $this->fixture->setExtractor($this->extractor);
        self::assertSame($this->extractor, $this->fixture->getExtractor());
    }

    public function testFilter()
    {
        $data = array('Foo' => 'Bar');
        $graph = \Mockery::mock('Fhaculty\Graph\Graph');
        $this->extractor->shouldReceive('extract')->with($graph)->once()->andReturn($data);
        $this->fixture->setExtractor($this->extractor);

        self::assertSame(json_encode($data), $this->fixture->filter($graph));
    }
}
