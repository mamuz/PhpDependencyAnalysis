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

namespace PhpDA\Service;

use PhpDA\Command\Analyze;
use PhpDA\Parser\AnalyzerInterface;
use PhpDA\Plugin\FactoryInterface;
use PhpDA\Writer\AdapterInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Parser;

class ApplicationFactory implements FactoryInterface
{
    /** @var FactoryInterface */
    private $analyzerFactory;

    /** @var FactoryInterface */
    private $writeAdapterFactory;

    public function __construct(
        FactoryInterface $analyzerFactory,
        FactoryInterface $writeAdapterFactory
    ) {
        $this->analyzerFactory = $analyzerFactory;
        $this->writeAdapterFactory = $writeAdapterFactory;
    }

    /**
     * @return Application
     */
    public function create()
    {
        $app = new Application;
        $app->add($this->createAnalyzeCommand());

        return $app;
    }

    /**
     * @return Analyze
     */
    protected function createAnalyzeCommand()
    {
        $command = new Analyze;

        $command->setConfigParser($this->createConfigParser());
        $command->setFinder($this->createFinder());
        $command->setAnalyzer($this->createAnalyzer());
        $command->setWriteAdapter($this->createWriteAdapter());

        return $command;
    }

    /**
     * @return Finder
     */
    private function createFinder()
    {
        return new Finder;
    }

    /**
     * @return Parser
     */
    private function createConfigParser()
    {
        return new Parser;
    }

    /**
     * @return AnalyzerInterface
     */
    private function createAnalyzer()
    {
        return $this->analyzerFactory->create();
    }

    /**
     * @return AdapterInterface
     */
    private function createWriteAdapter()
    {
        return $this->writeAdapterFactory->create();
    }
}
