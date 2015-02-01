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

namespace PhpDA\Writer\Strategy;

use Fhaculty\Graph\Edge\Directed;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;

class Json implements StrategyInterface
{
    /** @var array */
    private $data;

    public function filter(Graph $graph)
    {
        $this->extract($graph);

        if ($json = json_encode($this->data)) {
            return $json;
        }

        throw new \RuntimeException('Cannot create JSON');
    }

    /**
     * @param Graph $graph
     */
    private function extract(Graph $graph)
    {
        $this->data = array(
            'edges'      => array(),
            'vertices'   => array(),
            'attributes' => $graph->getAttributeBag()->getAttributes(),
        );

        $edges = $graph->getEdges();
        foreach ($edges as $edge) {
            /** @var Directed $edge */
            $this->addEdge($edge);
            $this->addVertex($edge->getVertexStart());
            $this->addVertex($edge->getVertexEnd());
        }
    }

    /**
     * @param Directed $edge
     */
    private function addEdge(Directed $edge)
    {
        $vertexStart = $edge->getVertexStart();
        $vertexEnd = $edge->getVertexEnd();
        $id = $vertexStart->getId() . '=>' . $vertexEnd->getId();

        $this->data['edges'][$id] = array(
            'from'              => $vertexStart->getId(),
            'to'                => $vertexEnd->getId(),
            'attributes'        => $edge->getAttributeBag()->getAttributes(),
            'locations'         => array(),
            'belongsToCycle'    => false,
            'cycles'            => array(),
            'invalidReference'  => false,
            'referenceMessages' => array(),
        );
    }

    /**
     * @param Vertex $vertex
     */
    private function addVertex(Vertex $vertex)
    {
        $id = $vertex->getId();

        if (!array_key_exists($id, $this->data['vertices'])) {
            $this->data['vertices'][$id] = array(
                'name'        => $id,
                'attributes'  => $vertex->getAttributeBag()->getAttributes(),
                'usesCount'   => $vertex->getEdgesOut()->count(),
                'usedByCount' => $vertex->getEdgesIn()->count(),
                'adtType'     => array(),
                'metadata'    => array(),
                'location'    => array(),
                'group'       => $vertex->getGroup(),
            );
        }
    }
}
