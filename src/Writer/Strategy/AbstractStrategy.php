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

namespace PhpDA\Writer\Strategy;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\GraphViz;
use PhpDA\Entity\AnalysisCollection;

abstract class AbstractStrategy implements StrategyInterface
{
    /** @var AnalysisCollection */
    private $analysisCollection;

    /** @var GraphViz */
    private $graphViz;

    /** @var callable */
    private $graphCreationCallback;

    public function __construct()
    {
        $this->graphCreationCallback = function (Graph $graph) {
            return new GraphViz($graph);
        };
    }

    /**
     * @param callable $graphCreationCallback
     * @throws \InvalidArgumentException
     */
    public function setGraphCreationCallback($graphCreationCallback)
    {
        if (!is_callable($graphCreationCallback)) {
            throw new \InvalidArgumentException('Argument must be callable');
        }

        $this->graphCreationCallback = $graphCreationCallback;
    }

    /**
     * @return callable
     */
    public function getGraphCreationCallback()
    {
        return $this->graphCreationCallback;
    }

    /**
     * @return AnalysisCollection
     */
    protected function getAnalysisCollection()
    {
        return $this->analysisCollection;
    }

    /**
     * @return GraphViz
     */
    protected function getGraphViz()
    {
        return $this->graphViz;
    }

    public function filter(AnalysisCollection $collection)
    {
        $this->analysisCollection = $collection;
        $graphCreationCallback = $this->getGraphCreationCallback();
        $this->graphViz = $graphCreationCallback($this->getAnalysisCollection()->getGraph());

        if (!$this->graphViz instanceof GraphViz) {
            throw new \RuntimeException('Created GraphViz is invalid');
        }

        return $this->createOutput();
    }

    /**
     * @return string
     */
    abstract protected function createOutput();
}
