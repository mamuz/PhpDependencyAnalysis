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

namespace PhpDATest\Command;

use PhpDA\Command\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $values = array(
            'mode'               => 'call',
            'source'             => 'mySource',
            'ignore'             => 'myIgnore',
            'formatter'          => 'myFormatter',
            'target'             => 'myTarget',
            'filePattern'        => 'myFilePattern',
            'groupLength'        => 4,
            'visitor'            => array('foo', 'baz'),
            'visitorOptions'     => array('bar'),
            'referenceValidator' => 'myValidator',
            'namespaceFilter'    => 'myFilter',
        );

        $config = new Config($values);

        foreach ($values as $property => $value) {
            $getter = 'get' . ucfirst($property);
            self::assertSame($value, $config->$getter());
        }
    }

    public function testOptionalConfigs()
    {
        $config = new Config(array());

        self::assertSame(array(), $config->getIgnore());
        self::assertSame(array(), $config->getVisitor());
        self::assertSame(array(), $config->getVisitorOptions());
        self::assertSame('usage', $config->getMode());
        self::assertSame(0, $config->getGroupLength());
        self::assertNull($config->getReferenceValidator());
        self::assertNull($config->getNamespaceFilter());
    }

    public function testInheritanceMode()
    {
        $config = new Config(array('mode' => 'inheritance'));
        self::assertSame('inheritance', $config->getMode());
    }

    public function testInvalidMode()
    {
        self::expectException('InvalidArgumentException');
        $config = new Config(array('mode' => 'foo'));

        $config->getMode();
    }

    public function testInvalidSource()
    {
        self::expectException('InvalidArgumentException');
        $config = new Config(array('source' => 1));

        $config->getSource();
    }

    public function testInvalidFormatter()
    {
        self::expectException('InvalidArgumentException');
        $config = new Config(array('formatter' => 1));

        $config->getFormatter();
    }

    public function testInvalidTarget()
    {
        self::expectException('InvalidArgumentException');
        $config = new Config(array('target' => 1));

        $config->getTarget();
    }

    public function testInvalidFilePattern()
    {
        self::expectException('InvalidArgumentException');
        $config = new Config(array('filePattern' => 1));

        $config->getFilePattern();
    }

    public function testInvalidIgnore()
    {
        self::expectException('InvalidArgumentException');
        $config = new Config(array('ignore' => 1));

        $config->getIgnore();
    }

    public function testInvalidGroupLength()
    {
        self::expectException('InvalidArgumentException');
        $config = new Config(array('groupLength' => 'test'));

        $config->getGroupLength();
    }

    public function testNumericGroupLength()
    {
        $config = new Config(array('groupLength' => '4'));
        self::assertSame(4, $config->getGroupLength());

        $config = new Config(array('groupLength' => 1.34));
        self::assertSame(1, $config->getGroupLength());
    }

    public function testInvalidVisitor()
    {
        self::expectException('InvalidArgumentException');
        $config = new Config(array('visitor' => 1));

        $config->getVisitor();
    }

    public function testInvalidVisitorOptions()
    {
        self::expectException('InvalidArgumentException');
        $config = new Config(array('visitorOptions' => 1));

        $config->getVisitorOptions();
    }

    public function testInvalidReferenceValidator()
    {
        self::expectException('InvalidArgumentException');
        $config = new Config(array('referenceValidator' => 1));

        $config->getReferenceValidator();
    }

    public function testInvalidNamespaceFilter()
    {
        self::expectException('InvalidArgumentException');
        $config = new Config(array('namespaceFilter' => 1));

        $config->getNamespaceFilter();
    }

    public function testHasVisitorOptionsForAggregation()
    {
        $config = new Config(array(
            'visitorOptions' => array(
                'foo' => array('excludePattern' => 'bar'),
                'baz' => array('minDepth' => 'bar'),
            ),
        ));
        self::assertFalse($config->hasVisitorOptionsForAggregation());

        $config = new Config(array(
            'visitorOptions' => array(
                'foo' => array('sliceOffset' => 'bar'),
            ),
        ));
        self::assertTrue($config->hasVisitorOptionsForAggregation());

        $config = new Config(array(
            'visitorOptions' => array(
                'foo' => array('sliceOffset' => '', 'slice' => null, 'sliceLength' => ''),
            ),
        ));
        self::assertFalse($config->hasVisitorOptionsForAggregation());

        $config = new Config(array(
            'visitorOptions' => array(
                'baz' => array('excludePattern' => 'bar'),
                'foo' => array('sliceLength' => 'bar'),
            ),
        ));
        self::assertTrue($config->hasVisitorOptionsForAggregation());

        $config = new Config(array(
            'visitorOptions' => array(
                'baz' => array('excludePattern' => 'bar', 'sliceLength' => 'bar'),
            ),
        ));
        self::assertTrue($config->hasVisitorOptionsForAggregation());
    }

    public function testSetGlobalVisitorOption()
    {
        $config = new Config(array(
            'visitorOptions' => array(
                'baz' => array('excludePattern' => 'bar'),
                'foo' => array('sliceLength' => 'bar'),
            ),
        ));

        $config->setGlobalVisitorOption('namespaceFilter', 'filter');
        $options = $config->getVisitorOptions();

        self::assertSame(array('excludePattern' => 'bar', 'namespaceFilter' => 'filter'), $options['baz']);
        self::assertSame(array('sliceLength' => 'bar', 'namespaceFilter' => 'filter'), $options['foo']);
    }
}
