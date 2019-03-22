<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2019 Daniel Kinzler
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

use Exception;
use PhpDA\Command\MessageInterface as Message;
use PhpDA\Command\Strategy\RenderFactory;
use PhpDA\Command\Strategy\StrategyInterface;
use PhpDA\Plugin\LoaderInterface;
use PhpDA\Writer\Strategy\Svg;
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
class Render extends Command
{
    const EXIT_SUCCESS = 0, EXIT_VIOLATION = 1, EXIT_EXCEPTION = 2;

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
        $this->addArgument(
            'source',
            InputArgument::REQUIRED,
            Message::CMD_RENDER_ARG_SOURCE
        );

        $this->addArgument(
            'target',
            InputArgument::REQUIRED,
            Message::CMD_RENDER_ARG_TARGET
        );

        $this->addOption(
            'config',
            'c',
            InputOption::VALUE_OPTIONAL,
            Message::CMD_RENDER_OPT_CONFIG
        );
        $this->addOption(
            'formatter',
            'f',
            InputOption::VALUE_OPTIONAL,
            Message::CMD_RENDER_OPT_FORMATTER,
            Svg::class
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $config = $this->createConfigBy($input);
            $this->addClassMapToClassLoaderFrom($config);
            $this->addLogLevelFormatsTo($output);
            $label = Message::NAME . ' (' . Version::read() . ')';

            $source = $input->getArgument('source');
            $target = $input->getArgument('target');

            $output->writeln($label . PHP_EOL);

            $output->writeln(sprintf(Message::RENDER_FROM_TO, $source, $target) . PHP_EOL);

            $options = [
                'source' => $source,
                'target' => $target,
                'config' => $config,
                'output' => $output
            ];

            if ($this->loadStrategy($options)->execute()) {
                return self::EXIT_SUCCESS;
            } else {
                return self::EXIT_VIOLATION;
            }
        } catch ( Exception $e) {
            throw new Exception('Execution failed', self::EXIT_EXCEPTION, $e);
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
        $configFilePath = $input->getOption('config');

        if ( !$configFilePath ) {
            $config = [];
        } else {
            if (strpos($configFilePath, '://') === false) {
                $this->configFilePath = realpath($configFilePath);
            }

            if (!is_readable($configFilePath)) {
                throw new \InvalidArgumentException('Configfile "' . $configFilePath . '" is not readable');
            }

            $config = $this->configParser->parse(file_get_contents($configFilePath));

            if (!is_array($config)) {
                throw new \InvalidArgumentException('Configuration is invalid');
            }
            $config = $this->normalizePathsIn($config, $configFilePath);

        }

        $config = array_merge($config, array_filter($input->getOptions()));
        $config = array_merge($config, array_filter($input->getArguments()));

        return new Config($config);
    }

    /**
     * @param array $config
     * @param string $base
     *
     * @return array
     */
    private function normalizePathsIn(array $config, $base)
    {
        if (isset($config['classMap']) && is_array($config['classMap'])) {
            foreach ($config['classMap'] as $class => $path) {
                $config['classMap'][$class] = $this->generateAbsolutePathFrom($path, $base);
            }
        }

        return $config;
    }

    /**
     * @param string $path
     * @param string $base
     *
     * @return bool
     */
    private function generateAbsolutePathFrom($path, $base)
    {
        if (!is_string($path)) {
            throw new \InvalidArgumentException('Path must be a string');
        }

        $path = trim($path);

        if ($path[0] === '/') {
            return $path;
        }

        if (defined('PHP_WINDOWS_VERSION_BUILD')
            && ($path[0] === '\\' || (strlen($path) >= 3 && preg_match('#^[A-Z]\:[/\\\]#i', substr($path, 0, 3))))
        ) {
            return $path;
        }

        if (strpos($path, '://') !== false) {
            return $path;
        }

        return $base . DIRECTORY_SEPARATOR . $path;
    }

    /**
     * @param Config $config
     */
    private function addClassMapToClassLoaderFrom(Config $config)
    {
        ApplicationFactory::$classLoader->addClassMap($config->getClassMap());
    }

    /**
     * @param array $options
     *
     * @return StrategyInterface
     */
    private function loadStrategy(array $options)
    {
        $factory = new RenderFactory();
        $strategy = $factory->create();
        $strategy->setOptions($options);
        return $strategy;
    }
}
