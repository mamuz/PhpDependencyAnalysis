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

use PhpDA\Parser\AnalyzerInterface;
use PhpDA\Writer\AdapterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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

    /** @var Finder|\Symfony\Component\Finder\SplFileInfo[] */
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
        $this->setName("analyze")->setDescription("Analyze php dependencies");
        $this->setHelp('Please visit https://github.com/mamuz/PhpDependencyAnalysis for detailed informations.');

        $this->addArgument(
            'config',
            InputArgument::OPTIONAL,
            'Path to yaml configuration file',
            './phpda.yml'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configFile = $input->getArgument('config');
        $this->bindConfigFrom($configFile);

        $output->writeln('PhpDependencyAnalyse ' . $this->getVersion() . ' by Marco Muths.' . PHP_EOL);
        $output->writeln('Configuration read from ' . realpath($configFile) . PHP_EOL);

        $progress = $this->getHelper('progress');
        $progress->start($output, iterator_count($this->finder));

        foreach ($this->finder as $file) {
            $this->analyzer->analyze($file);
            $progress->advance();
        }
        $this->writeAnalysis();

        $progress->finish();
        $output->writeln(PHP_EOL . 'Done' . PHP_EOL);
    }

    /**
     * @return string
     */
    private function getVersion()
    {
        return trim(file_get_contents(__DIR__ . '/../../VERSION'));
    }

    /**
     * @param string $configFile
     * @throws \InvalidArgumentException
     * @return void
     */
    private function bindConfigFrom($configFile)
    {
        $config = $this->configParser->parse(file_get_contents($configFile));

        if (!is_array($config)) {
            throw new \InvalidArgumentException('Configuration is invalid');
        }

        $this->config = new Config($config);

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
