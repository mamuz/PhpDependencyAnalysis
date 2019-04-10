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

namespace PhpDATest\Writer;

use PhpDA\Writer\Adapter;

class AdapterTest extends \PHPUnit_Framework_TestCase
{
    /** @var Adapter */
    protected $fixture;

    /** @var \PhpDA\Plugin\LoaderInterface | \Mockery\MockInterface */
    protected $loader;

    /** @var string */
    protected $file = 'baz';

    protected function setUp()
    {
        $this->file = __DIR__ . '/' . $this->file;
        $this->loader = \Mockery::mock('PhpDA\Plugin\LoaderInterface');
        $this->fixture = new Adapter($this->loader);
    }

    protected function tearDown()
    {
        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }

    public function testWriteWithLoadingWrongStrategy()
    {
        self::expectException('RuntimeException');

        $writerFqcn = 'foo';
        $graph = \Mockery::mock('Fhaculty\Graph\Graph');
        $writer = \Mockery::mock('PhpDA\Writer\Strategy\LoaderInterface');
        $this->loader->shouldReceive('get')->with($writerFqcn)->once()->andReturn($writer);

        $this->fixture->write($graph)->with($writerFqcn)->to($this->file);
    }

    public function testWriteWithFluentInterface()
    {
        $writerFqcn = 'foo';
        $content = 'bar';
        $graph = \Mockery::mock('Fhaculty\Graph\Graph');
        $writer = \Mockery::mock('PhpDA\Writer\Strategy\StrategyInterface');
        $writer->shouldReceive('filter')->once()->with($graph)->andReturn($content);
        $this->loader->shouldReceive('get')->with($writerFqcn)->once()->andReturn($writer);

        $this->fixture->write($graph)->with($writerFqcn)->to($this->file);
        self::assertSame($content, file_get_contents($this->file));
    }
}
