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

use Doctrine\Common\Collections\ArrayCollection;
use Fhaculty\Graph\Algorithm\ConnectedComponents;
use Fhaculty\Graph\Edge\Directed;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Set\Edges;
use PhpDA\Entity\Cycle;
use PhpDA\Entity\Edge;

class CycleDetector
{
    /** @var DependencyMapGenerator */
    private $dependencyMapGenerator;

    /** @var Graph */
    private $graph;

    /** @var Graph[] */
    private $disconnections;

    /** @var array */
    private $dependencyMap = array();

    /** @var Cycle[] */
    private $cycles = array();

    /** @var ArrayCollection */
    private $path;

    /** @var string[] */
    private $visitedEdges = array();

    public function __construct(DependencyMapGenerator $dependencyMapGenerator)
    {
        $this->dependencyMapGenerator = $dependencyMapGenerator;
        $this->path = new ArrayCollection;
    }

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
            $this->dependencyMap = $this->dependencyMapGenerator->buildBy($graph);
            foreach ($this->dependencyMap as $rootFqn => $dependencies) {
                $this->path->clear();
                $this->path->add($rootFqn);
                $this->walk($dependencies);
            }
        }
    }

    /**
     * @param array $dependencies
     */
    private function walk(array $dependencies)
    {
        foreach ($dependencies as $dependency) {
            $edge = new Edge($this->path->last(), $dependency);
            if (in_array($edge->toString(), $this->visitedEdges)) {
                continue;
            } else {
                $this->visitedEdges[] = $edge->toString();
            }
            $this->path->add($dependency);
            if ($dependency == $this->path->first()) {
                if ($this->path->count() > 1) {
                    $this->cycles[] = new Cycle($this->path->toArray());
                }
                $this->path->clear();
            } elseif (isset($this->dependencyMap[$dependency])) {
                $this->walk($this->dependencyMap[$dependency]);
            }
        }
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
        $allCycleEdges[] = array();

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
                $search = array(
                    $edge->getVertexStart()->getId(),
                    $edge->getVertexEnd()->getId()
                );
                return in_array($search, $allCycleEdges);
            }
        );
    }
}
