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

use PhpDA\Entity\Location;

class LocationTest extends \PHPUnit_Framework_TestCase
{
    /** @var Location */
    protected $fixture;

    /** @var \Symfony\Component\Finder\SplFileInfo | \Mockery\MockInterface */
    protected $file;

    /** @var array */
    protected $attributes = array(
        'startLine' => 30,
        'endLine'   => 40,
        'isComment' => true,
        'fqn'       => 'Foo\\Bar',
    );

    protected function setUp()
    {
        $this->file = \Mockery::mock('Symfony\Component\Finder\SplFileInfo');
        $this->fixture = new Location($this->file, $this->attributes);
    }

    public function testAccessFile()
    {
        $this->assertSame($this->file, $this->fixture->getFile());
    }

    public function testAccessIsComment()
    {
        $this->assertTrue($this->fixture->isComment());

        $attributes = array(
            'startLine' => 30,
            'endLine'   => 40,
            'fqn'       => 'Foo\\Bar',
        );
        $this->fixture = new Location($this->file, $attributes);
        $this->assertFalse($this->fixture->isComment());
    }

    public function testAccessStartAndEndLine()
    {
        $this->assertSame($this->attributes['startLine'], $this->fixture->getStartLine());
        $this->assertSame($this->attributes['endLine'], $this->fixture->getEndLine());
    }

    public function testAccessFqn()
    {
        $this->assertSame($this->attributes['fqn'], $this->fixture->getFqn());
    }

    public function testInvalidArgumentExceptionForMissingStartline()
    {
        $this->setExpectedException('InvalidArgumentException');
        $attributes = array('endLine' => 40, 'fqn' => 'Foo\\Bar');
        $this->fixture = new Location($this->file, $attributes);
    }

    public function testInvalidArgumentExceptionForMissingEndline()
    {
        $this->setExpectedException('InvalidArgumentException');
        $attributes = array('startLine' => 40, 'fqn' => 'Foo\\Bar');
        $this->fixture = new Location($this->file, $attributes);
    }

    public function testInvalidArgumentExceptionForMissingFqn()
    {
        $this->setExpectedException('InvalidArgumentException');
        $attributes = array('startLine' => 40, 'endLine' => 40);
        $this->fixture = new Location($this->file, $attributes);
    }
}
