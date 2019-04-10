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

use PhpDA\Layout\GraphViz;

class GraphVizTest extends \PHPUnit_Framework_TestCase
{
    /** @var GraphViz */
    protected $fixture;

    /** @var \Fhaculty\Graph\Graph | \Mockery\MockInterface */
    protected $graph;

    protected function setUp()
    {
        $this->fixture = new GraphViz();
    }

    protected function tearDown()
    {
        $this->fixture->setGroups(array());
        $this->fixture->setGroupLayout(array());
    }

    public function testExtendingFhacultyGraphViz()
    {
        self::assertInstanceOf('Graphp\GraphViz\GraphViz', $this->fixture);
    }

    public function testGroupLayouting()
    {
        $this->fixture->setGroups(array(1 => 'foo', '2' => 'bar'));
        $this->fixture->setGroupLayout(array('baz' => 'boo', 'attr' => 5));

        self::assertSame(3, GraphViz::escape(3));
        self::assertSame('"foo"', GraphViz::escape('foo'));
        self::assertSame('2', GraphViz::escape('2'));
        self::assertSame('1', GraphViz::escape('1'));

        $expected = '"foo"' . PHP_EOL . 'baz="boo";' . PHP_EOL . 'attr=5;';
        self::assertSame($expected, GraphViz::escape(1));
    }
}
