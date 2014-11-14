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
use PhpDA\Command\MessageInterface as Message;
use PhpDATest\Command\Stub\Analyze;

class AnalyzeTest extends \PHPUnit_Framework_TestCase
{
    /** @var Analyze */
    protected $fixture;

    /** @var \Symfony\Component\Yaml\Parser | \Mockery\MockInterface */
    protected $configParser;

    /** @var \PhpDA\Plugin\LoaderInterface | \Mockery\MockInterface */
    protected $strategyLoader;

    protected function setUp()
    {
        $this->configParser = \Mockery::mock('Symfony\Component\Yaml\Parser');
        $this->strategyLoader = \Mockery::mock('PhpDA\Plugin\LoaderInterface');

        $this->fixture = new Analyze('foo');
        $this->fixture->setConfigParser($this->configParser);
        $this->fixture->setStrategyLoader($this->strategyLoader);
    }

    public function testDefinition()
    {
        $definition = $this->fixture->getDefinition();

        $argument = $definition->getArgument('config');
        $this->assertSame(Message::ARGUMENT_CONFIG, $argument->getDescription());
        $this->assertRegExp('%src/Command/\.\./\.\./phpda\.yml\.dist$%', $argument->getDefault());
        $this->assertFalse($argument->isRequired());
        $this->assertFalse($argument->isArray());

        $option = $definition->getOption('mode');
        $this->assertSame('m', $option->getShortcut());
        $this->assertSame(Message::OPTION_MODE, $option->getDescription());
        $this->assertNull($option->getDefault());
        $this->assertTrue($option->isValueOptional());
        $this->assertFalse($option->isArray());

        $option = $definition->getOption('source');
        $this->assertSame('s', $option->getShortcut());
        $this->assertSame(Message::OPTION_SOURCE, $option->getDescription());
        $this->assertNull($option->getDefault());
        $this->assertTrue($option->isValueOptional());
        $this->assertFalse($option->isArray());

        $option = $definition->getOption('filePattern');
        $this->assertSame('p', $option->getShortcut());
        $this->assertSame(Message::OPTION_FILE_PATTERN, $option->getDescription());
        $this->assertNull($option->getDefault());
        $this->assertTrue($option->isValueOptional());
        $this->assertFalse($option->isArray());

        $option = $definition->getOption('ignore');
        $this->assertSame('i', $option->getShortcut());
        $this->assertSame(Message::OPTION_IGNORE, $option->getDescription());
        $this->assertNull($option->getDefault());
        $this->assertTrue($option->isValueOptional());
        $this->assertFalse($option->isArray());

        $option = $definition->getOption('formatter');
        $this->assertSame('f', $option->getShortcut());
        $this->assertSame(Message::OPTION_FORMATTER, $option->getDescription());
        $this->assertNull($option->getDefault());
        $this->assertTrue($option->isValueOptional());
        $this->assertFalse($option->isArray());

        $option = $definition->getOption('target');
        $this->assertSame('t', $option->getShortcut());
        $this->assertSame(Message::OPTION_TARGET, $option->getDescription());
        $this->assertNull($option->getDefault());
        $this->assertTrue($option->isValueOptional());
        $this->assertFalse($option->isArray());
    }

    public function testExecution()
    {
        $input = \Mockery::mock('Symfony\Component\Console\Input\InputInterface');
        $output = \Mockery::mock('Symfony\Component\Console\Output\OutputInterface');
        $output->shouldReceive('writeln');

        $configPath = __DIR__ . '/Stub/config.txt';
        $config = array('mode' => 'call', 'source' => '.', 'ignore' => 'dir1, dir2,dir3');
        $options = array('mode' => 'inheritance', 'source' => '.');

        $input->shouldReceive('getArgument')->with('config')->once()->andReturn($configPath);
        $input->shouldReceive('getOptions')->once()->andReturn($options);

        $this->configParser->shouldReceive('parse')->once()->with("stub\n")->andReturn($config);

        $testcase = $this;
        $this->strategyLoader->shouldReceive('get')->andReturnUsing(
            function ($fqn, $options) use ($testcase, $output) {
                $testcase->assertSame('PhpDA\\Command\\Strategy\\InheritanceFactory', $fqn);
                $testcase->assertSame($output, $options['output']);
                /** @var Config $config */
                $config = $options['config'];
                $testcase->assertSame('.', $config->getSource());
                $testcase->assertSame(array('dir1', 'dir2', 'dir3'), $config->getIgnore());
                $strategy = \Mockery::mock('PhpDA\Command\Strategy\StrategyInterface');
                $strategy->shouldReceive('execute')->once();
                return $strategy;
            }
        );

        $this->fixture->callExecute($input, $output);
    }

    public function testExecutionWithInvalidStrategy()
    {
        $this->setExpectedException('RuntimeException');

        $input = \Mockery::mock('Symfony\Component\Console\Input\InputInterface');
        $output = \Mockery::mock('Symfony\Component\Console\Output\OutputInterface');
        $output->shouldReceive('writeln');

        $configPath = __DIR__ . '/Stub/config.txt';
        $config = array('mode' => 'call');

        $input->shouldReceive('getArgument')->andReturn($configPath);
        $input->shouldReceive('getOptions')->andReturn(array());

        $this->configParser->shouldReceive('parse')->andReturn($config);

        $this->strategyLoader->shouldReceive('get')->andReturnUsing(
            function () {
                $strategy = new \stdClass;
                return $strategy;
            }
        );

        $this->fixture->callExecute($input, $output);
    }

    public function testExecutionWithInvalidConfig()
    {
        $this->setExpectedException('InvalidArgumentException');

        $input = \Mockery::mock('Symfony\Component\Console\Input\InputInterface');
        $output = \Mockery::mock('Symfony\Component\Console\Output\OutputInterface');
        $output->shouldReceive('writeln');

        $configPath = __DIR__ . '/Stub/config.txt';
        $input->shouldReceive('getArgument')->andReturn($configPath);

        $this->configParser->shouldReceive('parse')->andReturn(null);

        $this->fixture->callExecute($input, $output);
    }
}
