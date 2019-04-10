<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 Christian A. Wolf
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

use PhpDA\Entity\JsonSerializableFileInfo;

class JsonSerializableFileInfoTest extends \PHPUnit_Framework_TestCase
{
    /** @var JsonSerializableFileInfo */
    protected $fixture;

    /** @var \Symfony\Component\Finder\SplFileInfo | \Mockery\MockInterface */
    protected $file;

    protected function setUp()
    {
        $this->file = \Mockery::mock('Symfony\Component\Finder\SplFileInfo');
        $this->file->shouldReceive('__toString')->andReturn('filename');
        $this->file->shouldReceive('getPathname')->andReturn('pathname');
        $this->file->shouldReceive('getRelativePath')->andReturn('relative/path');
        $this->fixture = new JsonSerializableFileInfo($this->file);
    }

    public function testIsWrapper()
    {
        self::assertSame('pathname', $this->fixture->getFile()->getPathname());
        self::assertSame('relative/path', $this->fixture->getFile()->getRelativePath());
    }

    public function testIsSerializable()
    {
        self::assertInstanceOf('\JsonSerializable', $this->fixture);
        self::assertSame('filename', $this->fixture->jsonSerialize());
        self::assertSame('filename', $this->fixture->__toString());
    }
}
