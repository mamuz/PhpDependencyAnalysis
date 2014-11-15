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

namespace PhpDA\Command\Strategy;

use PhpDA\Command\Config;
use PhpDA\Command\MessageInterface as Message;
use PhpDA\Layout;
use PhpDA\Parser\AnalyzerInterface;
use PhpDA\Plugin\ConfigurableInterface;
use PhpDA\Writer\AdapterInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * @SuppressWarnings("PMD.CouplingBetweenObjects")
 */
abstract class AbstractStrategy implements ConfigurableInterface, StrategyInterface
{
    /** @var Config */
    private $config;

    /** @var OutputInterface */
    private $output;

    /** @var Finder */
    private $finder;

    /** @var integer */
    private $fileCnt = 0;

    /** @var AnalyzerInterface */
    private $analyzer;

    /** @var AdapterInterface */
    private $writeAdapter;

    /**
     * @param Finder            $finder
     * @param AnalyzerInterface $analyzer
     * @param AdapterInterface  $writeAdapter
     */
    public function __construct(Finder $finder, AnalyzerInterface $analyzer, AdapterInterface $writeAdapter)
    {
        $this->finder = $finder;
        $this->analyzer = $analyzer;
        $this->writeAdapter = $writeAdapter;

        $this->config = new Config(array());
        $this->output = new NullOutput;
    }

    public function setOptions(array $options)
    {
        if (isset($options['config']) && $options['config'] instanceof Config) {
            $this->config = $options['config'];
        }

        if (isset($options['output']) && $options['output'] instanceof OutputInterface) {
            $this->output = $options['output'];
        }

        $this->initFinder();
        $this->initLayout();
    }

    private function initFinder()
    {
        $this->getFinder()
            ->files()
            ->name($this->getConfig()->getFilePattern())
            ->in($this->getConfig()->getSource());

        if ($ignores = $this->getConfig()->getIgnore()) {
            $this->getFinder()->exclude($ignores);
        }

        $this->fileCnt = $this->getFinder()->count();
    }

    private function initLayout()
    {
        $analysisCollection = $this->getAnalyzer()->getAnalysisCollection();
        if ($this->getConfig()->hasVisitorOptionsForAggregation()) {
            $analysisCollection->setLayout(new Layout\Aggregation);
        } else {
            $analysisCollection->setLayout(new Layout\Standard);
        }
    }

    /**
     * @return AnalyzerInterface
     */
    protected function getAnalyzer()
    {
        return $this->analyzer;
    }

    /**
     * @return Config
     */
    protected function getConfig()
    {
        return $this->config;
    }

    /**
     * @return Finder
     */
    protected function getFinder()
    {
        return $this->finder;
    }

    /**
     * @return OutputInterface
     */
    protected function getOutput()
    {
        return $this->output;
    }

    /**
     * @return AdapterInterface
     */
    protected function getWriteAdapter()
    {
        return $this->writeAdapter;
    }

    public function execute()
    {
        if ($this->fileCnt < 1) {
            $this->getOutput()->writeln(Message::NOTHING_TO_PARSE . PHP_EOL);
            return;
        }

        $this->init();

        $progressHelper = $this->createProgressHelper();

        $progressHelper->start();
        $this->iterateFiles($progressHelper);
        $progressHelper->finish();

        $this->writeAnalysis();
        $this->getOutput()->writeln(PHP_EOL . Message::DONE . PHP_EOL);

        $this->writeAnalysisFailures();
    }

    abstract protected function init();

    /**
     * @return ProgressBar
     */
    private function createProgressHelper()
    {
        $progress = new ProgressBar($this->getOutput(), $this->fileCnt);
        $progress->setFormat(Message::PROGRESS_DISPLAY);

        if ($this->fileCnt > 5000) {
            $progress->setRedrawFrequency(100);
        }

        return $progress;
    }

    /**
     * @param ProgressBar $progressHelper
     */
    private function iterateFiles(ProgressBar $progressHelper)
    {
        foreach ($this->getFinder()->getIterator() as $file) {
            /** @var \Symfony\Component\Finder\SplFileInfo $file */
            if ($this->outputVerbosityIsVerbosed()) {
                $progressHelper->clear();
                $this->getOutput()->writeln("\x0D" . $file->getRealPath());
                $progressHelper->display();
            }
            $this->getAnalyzer()->analyze($file);
            $progressHelper->advance();
        }
    }

    /**
     * @return bool
     */
    private function outputVerbosityIsVerbosed()
    {
        return OutputInterface::VERBOSITY_VERBOSE <= $this->getOutput()->getVerbosity();
    }

    private function writeAnalysis()
    {
        $targetRealPath = realpath($this->getConfig()->getTarget());
        $this->getOutput()->writeln(PHP_EOL . PHP_EOL . Message::WRITE_GRAPH_TO . $targetRealPath);

        $this->getWriteAdapter()
            ->write($this->getAnalyzer()->getAnalysisCollection())
            ->with($this->getConfig()->getFormatter())
            ->to($this->getConfig()->getTarget());
    }

    private function writeAnalysisFailures()
    {
        if ($this->getAnalyzer()->getAnalysisCollection()->hasAnalysisFailures()) {
            $failures = $this->getAnalyzer()->getAnalysisCollection()->getAnalysisFailures();
            $this->getOutput()->writeln(Message::PARSE_ERRORS);
            foreach ($failures as $realpath => $error) {
                $this->getOutput()->writeln($realpath . ': ' . $error->getMessage());
            }
        }
    }
}
