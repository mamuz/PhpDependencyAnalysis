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

namespace PhpDA\Writer\Strategy;

use Fhaculty\Graph\Graph;
use PhpDA\Writer\Extractor\ExtractionInterface;
use PhpDA\Writer\Extractor\Graph as GraphExtractor;

class Json implements StrategyInterface
{
    /** @var ExtractionInterface */
    private $extractor;

    /**
     * @param ExtractionInterface $extractor
     */
    public function setExtractor(ExtractionInterface $extractor)
    {
        $this->extractor = $extractor;
    }

    /**
     * @return ExtractionInterface
     */
    public function getExtractor()
    {
        if (!$this->extractor instanceof ExtractionInterface) {
            $this->extractor = new GraphExtractor;
        }

        return $this->extractor;
    }

    public function filter(Graph $graph)
    {
        $data = $this->getExtractor()->extract($graph);

        if ($json = json_encode($data)) {
            return $json;
        }

        throw new \RuntimeException('Cannot create JSON');
    }
}
