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

namespace PhpDA\Writer;

use Fhaculty\Graph\Graph;
use PhpDA\Plugin\LoaderInterface;
use PhpDA\Writer\Strategy\StrategyInterface;

class Adapter implements AdapterInterface
{
    /** @var LoaderInterface */
    private $strategyLoader;

    /** @var Graph */
    private $graph;

    /** @var string */
    private $fqcn;

    /**
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader)
    {
        $this->strategyLoader = $loader;
    }

    public function write(Graph $graph)
    {
        $this->graph = $graph;
        return $this;
    }

    public function with($fqcn)
    {
        $this->fqcn = $fqcn;
        return $this;
    }

    public function to($file)
    {
        file_put_contents($file, $this->createContent(), LOCK_EX);
        return $this;
    }

    /**
     * @return string
     */
    private function createContent()
    {
        return $this->loadStrategy()->filter($this->graph);
    }

    /**
     * @throws \RuntimeException
     * @return StrategyInterface
     */
    private function loadStrategy()
    {
        $strategy = $this->strategyLoader->get($this->fqcn);

        if (!$strategy instanceof StrategyInterface) {
            throw new \RuntimeException(
                sprintf('Strategy \'%s\' must implement PhpDA\\Writer\\Strategy\\StrategyInterface', $this->fqcn)
            );
        }

        return $strategy;
    }
}
