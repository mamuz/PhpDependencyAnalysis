<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 Marco Muths
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

namespace PhpDA\Command;

use PhpDA\Command\MessageInterface as Message;
use PhpDA\Command\Strategy\StrategyInterface;
use PhpDA\Plugin\LoaderInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

/**
 * @SuppressWarnings("PMD.CouplingBetweenObjects")
 */
class Analyze extends Command
{
    const EXIT_SUCCESS = 0, EXIT_VIOLATION = 2;

    /** @var string */
    private $configFilePath;

    /** @var Parser */
    private $configParser;

    /** @var LoaderInterface */
    private $strategyLoader;

    /**
     * @param Parser $parser
     */
    public function setConfigParser(Parser $parser)
    {
        $this->configParser = $parser;
    }

    /**
     * @param LoaderInterface $loader
     */
    public function setStrategyLoader(LoaderInterface $loader)
    {
        $this->strategyLoader = $loader;
    }

    protected function configure()
    {
        $defaultConfig = __DIR__ . '/../../phpda.yml.dist';

        $this->addArgument('config', InputArgument::OPTIONAL, Message::ARGUMENT_CONFIG, $defaultConfig);
        $this->addOption('mode', 'm', InputOption::VALUE_OPTIONAL, Message::OPTION_MODE);
        $this->addOption('source', 's', InputOption::VALUE_OPTIONAL, Message::OPTION_SOURCE);
        $this->addOption('filePattern', 'p', InputOption::VALUE_OPTIONAL, Message::OPTION_FILE_PATTERN);
        $this->addOption('ignore', 'i', InputOption::VALUE_OPTIONAL, Message::OPTION_IGNORE);
        $this->addOption('formatter', 'f', InputOption::VALUE_OPTIONAL, Message::OPTION_FORMATTER);
        $this->addOption('target', 't', InputOption::VALUE_OPTIONAL, Message::OPTION_TARGET);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->addLogLevelFormatsTo($output);
        $config = $this->createConfigBy($input);

        $output->writeln($this->getDescription() . PHP_EOL);
        $output->writeln(Message::READ_CONFIG_FROM . $this->configFilePath . PHP_EOL);

        $strategyOptions = array(
            'config'      => $config,
            'output'      => $output,
            'layoutLabel' => $this->getDescription(),
        );

        if ($this->loadStrategy($config->getMode(), $strategyOptions)->execute()) {
            return self::EXIT_SUCCESS;
        } else {
            return self::EXIT_VIOLATION;
        }
    }

    /**
     * @param OutputInterface $output
     */
    private function addLogLevelFormatsTo(OutputInterface $output)
    {
        $error = new OutputFormatterStyle('white', 'red');
        $warning = new OutputFormatterStyle('red', 'yellow');
        $info = new OutputFormatterStyle('white', 'green');
        $debug = new OutputFormatterStyle('magenta');

        $formatter = $output->getFormatter();

        $formatter->setStyle(LogLevel::EMERGENCY, $error);
        $formatter->setStyle(LogLevel::ALERT, $error);
        $formatter->setStyle(LogLevel::CRITICAL, $error);
        $formatter->setStyle(LogLevel::WARNING, $warning);
        $formatter->setStyle(LogLevel::NOTICE, $info);
        $formatter->setStyle(LogLevel::DEBUG, $debug);
    }

    /**
     * @param InputInterface $input
     * @throws \InvalidArgumentException
     * @return Config
     */
    private function createConfigBy(InputInterface $input)
    {
        $this->configFilePath = realpath($input->getArgument('config'));
        $config = $this->configParser->parse(file_get_contents($this->configFilePath));

        if (!is_array($config)) {
            throw new \InvalidArgumentException('Configuration is invalid');
        }

        $config = array_merge($config, array_filter($input->getOptions()));

        if (isset($config['ignore']) && !is_array($config['ignore'])) {
            $config['ignore'] = array_map('trim', explode(',', $config['ignore']));
        }

        return new Config($config);
    }

    /**
     * @param string $type
     * @param array  $options
     * @throws \RuntimeException
     * @return StrategyInterface
     */
    private function loadStrategy($type, array $options = null)
    {
        $fqcn = 'PhpDA\\Command\\Strategy\\' . ucfirst($type) . 'Factory';
        $strategy = $this->strategyLoader->get($fqcn, $options);

        if (!$strategy instanceof StrategyInterface) {
            throw new \RuntimeException(
                sprintf('Strategy \'%s\' must implement PhpDA\\Command\\Strategy\\StrategyInterface', $fqcn)
            );
        }

        return $strategy;
    }
}
