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

namespace PhpDA\Command\Strategy;

use Fhaculty\Graph\Graph;
use PhpDA\Layout\Builder;
use PhpDA\Layout\Helper\CycleDetector;
use PhpDA\Layout\Helper\GroupGenerator;
use PhpDA\Parser\AnalyzerFactory;
use PhpDA\Plugin\FactoryInterface;
use PhpDA\Plugin\Loader;
use PhpDA\Writer\Adapter;
use Symfony\Component\Finder\Finder;

/**
 * @SuppressWarnings("PMD.CouplingBetweenObjects")
 */
abstract class AbstractFactory implements FactoryInterface
{
    /**
     * @return Finder
     */
    protected function createFinder()
    {
        return new Finder;
    }

    /**
     * @return \PhpDA\Parser\Analyzer
     */
    protected function createAnalyzer()
    {
        $analyzerFactory = new AnalyzerFactory;

        return $analyzerFactory->create();
    }

    /**
     * @return Builder
     */
    protected function createGraphBuilder()
    {
        $cycleDetector = new CycleDetector;

        return new Builder(new Graph, new GroupGenerator, $cycleDetector);
    }

    /**
     * @return Adapter
     */
    protected function createWriteAdapter()
    {
        return new Adapter($this->createLoader());
    }

    /**
     * @return Loader
     */
    protected function createLoader()
    {
        return new Loader;
    }
}
