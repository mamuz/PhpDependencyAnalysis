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

namespace PhpDA\Command;

use PhpDA\Command\MessageInterface as Message;
use PhpDA\Parser\AnalyzerInterface;
use PhpDA\Writer\AdapterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Parser;

/**
 * @SuppressWarnings("PMD.CouplingBetweenObjects")
 */
class Analyze extends Command
{
    /** @var Config */
    private $config;

    /** @var Parser */
    private $configParser;

    /** @var Finder */
    private $finder;

    /** @var AnalyzerInterface */
    private $analyzer;

    /** @var AdapterInterface */
    private $writeAdapter;

    /**
     * @param Parser $parser
     * @return void
     */
    public function setConfigParser(Parser $parser)
    {
        $this->configParser = $parser;
    }

    /**
     * @param Finder $finder
     * @return void
     */
    public function setFinder(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * @param AdapterInterface $writeAdapter
     * @return void
     */
    public function setWriteAdapter(AdapterInterface $writeAdapter)
    {
        $this->writeAdapter = $writeAdapter;
    }

    /**
     * @param AnalyzerInterface $analyzer
     * @return void
     */
    public function setAnalyzer(AnalyzerInterface $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    protected function configure()
    {
        $this->addArgument('config', InputArgument::OPTIONAL, Message::ARGUMENT_CONFIG, './phpda.yml');
        $this->addOption('source', 's', InputOption::VALUE_OPTIONAL, Message::OPTION_SOURCE);
        $this->addOption('filePattern', 'p', InputOption::VALUE_OPTIONAL, Message::OPTION_FILE_PATTERN);
        $this->addOption('ignore', 'i', InputOption::VALUE_OPTIONAL, Message::OPTION_IGNORE);
        $this->addOption('formatter', 'f', InputOption::VALUE_OPTIONAL, Message::OPTION_FORMATTER);
        $this->addOption('target', 't', InputOption::VALUE_OPTIONAL, Message::OPTION_TARGET);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configFile = $input->getArgument('config');
        $this->bindConfigFrom($configFile, $input->getOptions());

        $output->writeln($this->getDescription() . PHP_EOL);
        $output->writeln(Message::READ_CONFIG_FROM . realpath($configFile) . PHP_EOL);

        $progress = new ProgressBar($output, iterator_count($this->finder));
        $progress->setFormat(Message::PROGRESS_DISPLAY);
        $progress->start();
        foreach ($this->finder as $file) {
            /** @var \Symfony\Component\Finder\SplFileInfo $file */
            if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
                $progress->clear();
                $output->writeln($file->getPath());
                $progress->display();
            }
            $this->analyzer->analyze($file);
            $progress->advance();
        }
        $progress->finish();

        $output->writeln(PHP_EOL . PHP_EOL . Message::WRITE_GRAPH_TO . realpath($this->config->getTarget()));
        $this->writeAnalysis();
        $output->writeln(PHP_EOL . Message::DONE . PHP_EOL);
    }

    /**
     * @param string $configFile
     * @param array  $options
     * @throws \InvalidArgumentException
     * @return void
     */
    private function bindConfigFrom($configFile, array $options)
    {
        $config = $this->configParser->parse(file_get_contents($configFile));

        if (!is_array($config)) {
            throw new \InvalidArgumentException('Configuration is invalid');
        }

        if (isset($options['ignore'])) {
            $options['ignore'] = explode(',', $options['ignore']);
        }
        $this->config = new Config(array_merge($config, array_filter($options)));

        $this->finder
            ->files()
            ->name($this->config->getFilePattern())
            ->in($this->config->getSource());

        if ($ignores = $this->config->getIgnore()) {
            $this->finder->exclude($ignores);
        }

        $this->analyzer->getNodeTraverser()->bindVisitors(
            $this->config->getVisitor(),
            $this->config->getVisitorOptions()
        );
    }

    /**
     * @return void
     */
    private function writeAnalysis()
    {
        $this->writeAdapter
            ->write($this->analyzer->getAnalysisCollection())
            ->with($this->config->getFormatter())
            ->to($this->config->getTarget());
    }
}
