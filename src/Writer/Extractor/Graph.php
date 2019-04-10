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

namespace PhpDA\Writer\Extractor;

use Fhaculty\Graph\Edge\Directed;
use Fhaculty\Graph\Graph as FhacultyGraph;
use Fhaculty\Graph\Vertex;

class Graph implements ExtractionInterface
{
    /** @var array */
    private $data;

    public function extract(FhacultyGraph $graph)
    {
        $this->data = [
            'edges'    => [],
            'vertices' => [],
            'cycles'   => $this->extractEntities($graph->getAttribute('cycles', [])),
            'groups'   => $graph->getAttribute('graphviz.groups', []),
            'log'      => $graph->getAttribute('logEntries', []),
            'label'    => $graph->getAttribute('graphviz.graph.label'),
        ];

        $edges = $graph->getEdges();
        foreach ($edges as $edge) {
            /** @var Directed $edge */
            $this->addEdge($edge);
            $this->addVertex($edge->getVertexStart());
            $this->addVertex($edge->getVertexEnd());
        }

        ksort($this->data['edges']);
        ksort($this->data['vertices']);

        return $this->data;
    }

    /**
     * @param Directed $edge
     */
    private function addEdge(Directed $edge)
    {
        $id = $edge->getVertexStart()->getId() . '=>' . $edge->getVertexEnd()->getId();
        $this->data['edges'][$id] = $this->extractEdge($edge);
    }

    /**
     * @param Vertex $vertex
     */
    private function addVertex(Vertex $vertex)
    {
        $id = $vertex->getId();
        if (!array_key_exists($id, $this->data['vertices'])) {
            $this->data['vertices'][$id] = $this->extractVertex($vertex);
        }
    }

    /**
     * @param Directed $edge
     * @return array
     */
    private function extractEdge(Directed $edge)
    {
        return [
            'from'                       => $edge->getVertexStart()->getId(),
            'to'                         => $edge->getVertexEnd()->getId(),
            'locations'                  => $this->extractEntities($edge->getAttribute('locations', [])),
            'belongsToCycle'             => $edge->getAttribute('belongsToCycle', false),
            'referenceValidatorMessages' => $edge->getAttribute('referenceValidatorMessages'),
        ];
    }

    /**
     * @param Vertex $vertex
     * @return array
     */
    private function extractVertex(Vertex $vertex)
    {
        $locations = $this->extractEntities($vertex->getAttribute('locations', []));

        return [
            'name'        => $vertex->getId(),
            'usedByCount' => $vertex->getEdgesIn()->count(),
            'adt'         => $vertex->getAttribute('adt', []),
            'location'    => array_shift($locations),
            'group'       => $vertex->getGroup(),
        ];
    }

    /**
     * @param \PhpDA\Entity\Location[] | \PhpDA\Entity\Cycle[] $entities
     * @return array
     */
    private function extractEntities(array $entities)
    {
        $data = [];
        foreach ($entities as $entity) {
            $data[] = $entity->toArray();
        }

        return $data;
    }
}
