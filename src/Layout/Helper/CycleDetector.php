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

namespace PhpDA\Layout\Helper;

use Fhaculty\Graph\Edge\Directed;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Set\Edges;
use Graphp\Algorithms\ConnectedComponents;
use PhpDA\Entity\Cycle;

class CycleDetector
{
    /** @var Graph */
    private $graph;

    /** @var Graph[] */
    private $disconnections;

    /** @var Cycle[] */
    private $cycles = [];

    /**
     * @param Graph $graph
     * @return CycleDetector
     */
    public function inspect(Graph $graph)
    {
        $this->graph = $graph;
        $this->disconnectGraph();
        $this->findInDisconnections();

        return $this;
    }

    private function disconnectGraph()
    {
        $components = new ConnectedComponents($this->graph);
        $this->disconnections = $components->createGraphsComponents();
    }

    private function findInDisconnections()
    {
        foreach ($this->disconnections as $graph) {
            $edges = $graph->getEdges();
            foreach ($edges as $edge) {
                $this->walkOn($edge);
            }
        }
    }

    /**
     * @param Directed $edge
     * @param array    $path
     */
    private function walkOn(Directed $edge, array $path = [])
    {
        $vertexStart = $edge->getVertexStart()->getId();
        if (in_array($vertexStart, $path)) {
            $path[] = $vertexStart;
            $path = array_slice($path, array_search($vertexStart, $path));
            $this->addCycle($path);
        } else {
            $path[] = $vertexStart;
            $edgesOut = $edge->getVertexEnd()->getEdgesOut();
            foreach ($edgesOut as $edgeOut) {
                $this->walkOn($edgeOut, $path);
            }
        }
    }

    /**
     * @param array $path
     */
    private function addCycle(array $path)
    {
        foreach ($this->cycles as $cycle) {
            $pathUnique = array_unique($path);
            $cycleUnique = array_unique($cycle->toArray());
            if (count($pathUnique) === count($cycleUnique)
                && !array_diff($pathUnique, $cycleUnique)
            ) {
                return;
            }
        }

        $this->cycles[] = new Cycle($path);
    }

    /**
     * @return Cycle[]
     */
    public function getCycles()
    {
        return $this->cycles;
    }

    /**
     * @return Edges
     */
    public function getCycledEdges()
    {
        $allCycleEdges = [];

        foreach ($this->getCycles() as $cycle) {
            $cycledEdges = $cycle->getEdges();
            foreach ($cycledEdges as $cycledEdge) {
                if (!in_array($cycledEdge, $allCycleEdges)) {
                    $allCycleEdges[] = $cycledEdge->toArray();
                }
            }
        }

        return $this->graph->getEdges()->getEdgesMatch(
            function (Directed $edge) use ($allCycleEdges) {
                $search = [
                    $edge->getVertexStart()->getId(),
                    $edge->getVertexEnd()->getId(),
                ];
                return in_array($search, $allCycleEdges);
            }
        );
    }
}
