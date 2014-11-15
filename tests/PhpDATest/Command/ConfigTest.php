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

namespace PhpDATest\Command;

use PhpDA\Command\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $values = array(
            'mode'           => 'call',
            'source'         => 'mySource',
            'ignore'         => 'myIgnore',
            'formatter'      => 'myFormatter',
            'target'         => 'myTarget',
            'filePattern'    => 'myFilePattern',
            'visitor'        => array('foo', 'baz'),
            'visitorOptions' => array('bar'),
        );

        $config = new Config($values);

        foreach ($values as $property => $value) {
            $getter = 'get' . ucfirst($property);
            $this->assertSame($value, $config->$getter());
        }
    }

    public function testOptionalConfigs()
    {
        $config = new Config(array());

        $this->assertSame(array(), $config->getIgnore());
        $this->assertSame(array(), $config->getVisitor());
        $this->assertSame(array(), $config->getVisitorOptions());
        $this->assertSame('usage', $config->getMode());
    }

    public function testInheritanceMode()
    {
        $config = new Config(array('mode' => 'inheritance'));
        $this->assertSame('inheritance', $config->getMode());
    }

    public function testInvalidMode()
    {
        $this->setExpectedException('InvalidArgumentException');
        $config = new Config(array('mode' => 'foo'));

        $config->getMode();
    }

    public function testInvalidSource()
    {
        $this->setExpectedException('InvalidArgumentException');
        $config = new Config(array('source' => 1));

        $config->getSource();
    }

    public function testInvalidFormatter()
    {
        $this->setExpectedException('InvalidArgumentException');
        $config = new Config(array('formatter' => 1));

        $config->getFormatter();
    }

    public function testInvalidTarget()
    {
        $this->setExpectedException('InvalidArgumentException');
        $config = new Config(array('target' => 1));

        $config->getTarget();
    }

    public function testInvalidFilePattern()
    {
        $this->setExpectedException('InvalidArgumentException');
        $config = new Config(array('filePattern' => 1));

        $config->getFilePattern();
    }

    public function testInvalidIgnore()
    {
        $this->setExpectedException('InvalidArgumentException');
        $config = new Config(array('ignore' => 1));

        $config->getIgnore();
    }

    public function testInvalidVisitor()
    {
        $this->setExpectedException('InvalidArgumentException');
        $config = new Config(array('visitor' => 1));

        $config->getVisitor();
    }

    public function testInvalidVisitorOptions()
    {
        $this->setExpectedException('InvalidArgumentException');
        $config = new Config(array('visitorOptions' => 1));

        $config->getVisitorOptions();
    }

    public function testHasVisitorOptionsForAggregation()
    {
        $config = new Config(array(
            'visitorOptions' => array(
                'foo' => array('excludePattern' => 'bar'),
                'baz' => array('minDepth' => 'bar'),
            )
        ));
        $this->assertFalse($config->hasVisitorOptionsForAggregation());

        $config = new Config(array(
            'visitorOptions' => array(
                'foo' => array('sliceOffset' => 'bar'),
            )
        ));
        $this->assertTrue($config->hasVisitorOptionsForAggregation());

        $config = new Config(array(
            'visitorOptions' => array(
                'foo' => array('sliceOffset' => '', 'slice' => null, 'sliceLength' => ''),
            )
        ));
        $this->assertFalse($config->hasVisitorOptionsForAggregation());

        $config = new Config(array(
            'visitorOptions' => array(
                'baz' => array('excludePattern' => 'bar'),
                'foo' => array('sliceLength' => 'bar'),
            )
        ));
        $this->assertTrue($config->hasVisitorOptionsForAggregation());

        $config = new Config(array(
            'visitorOptions' => array(
                'baz' => array('excludePattern' => 'bar', 'sliceLength' => 'bar'),
            )
        ));
        $this->assertTrue($config->hasVisitorOptionsForAggregation());
    }
}
