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
    const EXIT_SUCCESS = 0, EXIT_VIOLATION = 1, EXIT_EXCEPTION = 2;
    const DEFAULT_CONFIGURATION_FILE_NAME = 'phpda';

    /** @var string */
    private $defaultConfigFilePath = __DIR__ . '/../../phpda.yml.dist';

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
        if (strpos($this->defaultConfigFilePath, '://') === false) {
            $this->defaultConfigFilePath = realpath($this->defaultConfigFilePath);
        }

        $this->addArgument(
            'config',
            InputArgument::OPTIONAL,
            Message::CMD_ANALYZE_ARG_CONFIG,
            $this->getDefaultConfigFilePath()
        );

        $this->addOption('mode', 'm', InputOption::VALUE_OPTIONAL, Message::CMD_ANALYZE_OPT_MODE);
        $this->addOption('source', 's', InputOption::VALUE_OPTIONAL, Message::CMD_ANALYZE_OPT_SOURCE);
        $this->addOption('filePattern', 'p', InputOption::VALUE_OPTIONAL, Message::CMD_ANALYZE_OPT_FILE_PATTERN);
        $this->addOption('ignore', 'i', InputOption::VALUE_OPTIONAL, Message::CMD_ANALYZE_OPT_IGNORE);
        $this->addOption('formatter', 'f', InputOption::VALUE_OPTIONAL, Message::CMD_ANALYZE_OPT_FORMATTER);
        $this->addOption('target', 't', InputOption::VALUE_OPTIONAL, Message::CMD_ANALYZE_OPT_TARGET);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $config = $this->createConfigBy($input);
            $this->addClassMapToClassLoaderFrom($config);
            $this->addLogLevelFormatsTo($output);
            $label = Message::NAME . ' ' . Version::read();

            $output->writeln($label . PHP_EOL);
            $output->writeln(sprintf(Message::READ_CONFIG_FROM, $this->configFilePath) . PHP_EOL);

            $strategyOptions = [
                'config'      => $config,
                'output'      => $output,
                'layoutLabel' => $label,
            ];

            if ($this->loadStrategy($config->getMode(), $strategyOptions)->execute()) {
                return self::EXIT_SUCCESS;
            } else {
                return self::EXIT_VIOLATION;
            }
        } catch (\Throwable $e) {
            throw new \Exception('Execution failed', self::EXIT_EXCEPTION, $e);
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
        $this->configFilePath = trim($input->getArgument('config'));

        if (strpos($this->configFilePath, '://') === false) {
            $this->configFilePath = realpath($this->configFilePath);
        }

        if (!is_readable($this->configFilePath)) {
            throw new \InvalidArgumentException('Configfile "' . $this->configFilePath . '" is not readable');
        }

        $config = $this->configParser->parse(file_get_contents($this->configFilePath));

        if (!is_array($config)) {
            throw new \InvalidArgumentException('Configuration is invalid');
        }

        $config = $this->normalizePathsIn($config);

        $config = array_merge($config, array_filter($input->getOptions()));

        if (isset($config['ignore']) && !is_array($config['ignore'])) {
            $config['ignore'] = array_map('trim', explode(',', $config['ignore']));
        }

        return new Config($config);
    }

    /**
     * @param array $config
     * @return array
     */
    private function normalizePathsIn(array $config)
    {
        $config['source'] = !isset($config['source']) ?: $this->generateAbsolutePathFrom($config['source']);
        $config['target'] = !isset($config['target']) ?: $this->generateAbsolutePathFrom($config['target']);

        if (isset($config['classMap']) && is_array($config['classMap'])) {
            foreach ($config['classMap'] as $class => $path) {
                $config['classMap'][$class] = $this->generateAbsolutePathFrom($path);
            }
        }

        return $config;
    }

    /**
     * @param string $path
     * @return bool
     * @throws \InvalidArgumentException
     */
    private function generateAbsolutePathFrom($path)
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

        if ($this->configFilePath == $this->defaultConfigFilePath) {
            return getcwd() . DIRECTORY_SEPARATOR . $path;
        }

        return dirname($this->configFilePath) . DIRECTORY_SEPARATOR . $path;
    }

    /**
     * @param Config $config
     */
    private function addClassMapToClassLoaderFrom(Config $config)
    {
        ApplicationFactory::$classLoader->addClassMap($config->getClassMap());
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

    /**
     * @return string
     */
    private function getDefaultConfigFilePath()
    {
        $configFilePaths = glob(getcwd(). DIRECTORY_SEPARATOR . self::DEFAULT_CONFIGURATION_FILE_NAME . '.[yml|yaml]*');

        if (count($configFilePaths) > 0) {
            return $configFilePaths[0];
        }

        return $this->defaultConfigFilePath;
    }
}
