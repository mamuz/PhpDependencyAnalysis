<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 Marco Muths
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

namespace PhpDA\Layout\Helper;

use Fhaculty\Graph\Edge\Directed;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;

class DependencyMapGenerator
{
    /** array */
    private $map;

    /** Vertex[] */
    private $vertices;

    /**
     * @param Graph $graph
     * @return array
     */
    public function buildBy(Graph $graph)
    {
        $this->map = array();
        $this->vertices = $graph->getVertices();

        $this->buildMap();
        $this->removeUnrelatedItemsFromMap();

        return $this->map;
    }

    private function buildMap()
    {
        foreach ($this->vertices as $vertex) {
            /** @var Vertex $vertex */
            $edgesOut = $vertex->getEdgesOut();
            $verticesTarget = array();
            foreach ($edgesOut as $edge) {
                /** @var Directed $edge */
                $verticesTarget[] = $edge->getVertexEnd()->getId();
            }
            if ($verticesTarget) {
                $this->map[$vertex->getId()] = $verticesTarget;
            }
        }
    }

    private function removeUnrelatedItemsFromMap()
    {
        foreach ($this->map as $rootFqn => $dependencies) {
            foreach ($dependencies as $index => $fqn) {
                if (!isset($this->map[$fqn])) {
                    unset($this->map[$rootFqn][$index]);
                }
            }
        }
    }
}
