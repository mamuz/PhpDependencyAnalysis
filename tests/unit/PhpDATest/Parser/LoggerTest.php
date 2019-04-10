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

namespace PhpDATest\Parser;

use PhpDA\Parser\Logger;
use Psr\Log\LogLevel;

class LoggerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Logger */
    protected $fixture;

    /** @var \Symfony\Component\Finder\SplFileInfo | \Mockery\MockInterface */
    protected $file;

    protected function setUp()
    {
        $this->file = \Mockery::mock('Symfony\Component\Finder\SplFileInfo');
        $this->fixture = new Logger;
    }

    public function testLogging()
    {
        self::assertSame('', $this->fixture->toString());
        self::assertTrue($this->fixture->isEmpty());

        $this->fixture->log(LogLevel::CRITICAL, 'CRITICALfoo', array('CRITICALbar'));
        $this->fixture->log(LogLevel::NOTICE, 'NOTICEfoo', array('NOTICEbar'));
        $this->fixture->log(LogLevel::EMERGENCY, 'EMERGENCYfoo', array('EMERGENCYbar'));

        self::assertNotEmpty($this->fixture->toString());
        self::assertFalse($this->fixture->isEmpty());
    }

    public function testLoggingWithWrapping()
    {
        $this->file->shouldReceive('__toString')->andReturn('filename');

        self::assertSame('', $this->fixture->toString());
        self::assertTrue($this->fixture->isEmpty());

        $this->fixture->log(LogLevel::CRITICAL, 'CRITICALfoo', array($this->file));
        $this->fixture->log(LogLevel::NOTICE, 'NOTICEfoo', array('NOTICEbar' => $this->file));

        self::assertNotEmpty($this->fixture->toString());
        self::assertFalse($this->fixture->isEmpty());
    }
}
