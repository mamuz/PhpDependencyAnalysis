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

namespace PhpDATest\Plugin;

use PhpDA\Plugin\Loader;

class LoaderTest extends \PHPUnit_Framework_TestCase
{
    /** @var Loader */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new Loader;
    }

    public function testGetWithNotExisingClassfile()
    {
        self::expectException('RuntimeException');
        $this->fixture->get('FooBar');
    }

    public function testGetPluginWithClassConstructorWithParams()
    {
        self::expectException('RuntimeException');
        $this->fixture->get('PhpDATest\Plugin\Stub\ConstructerParam');
    }

    public function testGetPluginWithClassConstructorWithoutParams()
    {
        $fqcn = 'PhpDATest\Plugin\Stub\Constructer';
        $plugin = $this->fixture->get($fqcn);

        self::assertInstanceOf($fqcn, $plugin);
    }

    public function testGetPluginWithoutConstructor()
    {
        $fqcn = 'PhpDATest\Plugin\Stub\WithoutConstructer';
        $plugin = $this->fixture->get($fqcn);

        self::assertInstanceOf($fqcn, $plugin);
    }

    public function testGetPluginWithFactory()
    {
        $plugin = $this->fixture->get('PhpDATest\Plugin\Stub\Factory');

        self::assertInstanceOf('stdClass', $plugin);
    }

    public function testGetPluginWithMutateOptions()
    {
        $options = array('foo', 'bar');
        /** @var \PhpDATest\Plugin\Stub\Option $plugin */
        $plugin = $this->fixture->get('PhpDATest\Plugin\Stub\Option', $options);

        self::assertSame($options, $plugin->getOptions());
    }

    public function testGetPluginWithFactoryAndMutateOptions()
    {
        $options = array('foo', 'bar');
        /** @var \PhpDATest\Plugin\Stub\Option $plugin */
        $plugin = $this->fixture->get('PhpDATest\Plugin\Stub\OptionFactory', $options);

        self::assertSame($options, $plugin->getOptions());
    }

    public function testGetPluginWithLoggerAwarenessAndNotHavingLogger()
    {
        /** @var \PhpDATest\Plugin\Stub\LoggerAware $plugin */
        $plugin = $this->fixture->get('PhpDATest\Plugin\Stub\LoggerAware');

        self::assertNull($plugin->getLogger());
    }

    public function testGetPluginWithLoggerAwareness()
    {
        $logger = \Mockery::mock('Psr\Log\LoggerInterface');
        $this->fixture->setLogger($logger);
        /** @var \PhpDATest\Plugin\Stub\LoggerAware $plugin */
        $plugin = $this->fixture->get('PhpDATest\Plugin\Stub\LoggerAware');

        self::assertSame($logger, $plugin->getLogger());
    }

    public function testGetPluginWithFactoryAndLoggerAwareness()
    {
        $logger = \Mockery::mock('Psr\Log\LoggerInterface');
        $this->fixture->setLogger($logger);
        /** @var \PhpDATest\Plugin\Stub\LoggerAware $plugin */
        $plugin = $this->fixture->get('PhpDATest\Plugin\Stub\LoggerAwareFactory');

        self::assertSame($logger, $plugin->getLogger());
    }
}
