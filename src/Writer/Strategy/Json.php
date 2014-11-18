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
use PhpDA\Entity\AnalysisCollection;

/**
 * Class JsonStrategy
 * Gives you a json encoded file. Every id of a vertex in the graph is a root identifier in the json file. Every first
 * level vertex (it has an edge TO the vertex) is in the collection to that identifier. E.g.:
 *
 * {
 *   "firstThing" :
 *      [
 *          "dependencyOfFirstThing1",
 *          "dependencyOfFirstThing2"
 *      ]
 * }
 *
 * @package PhpDA\Writer\Strategy
 */
class Json implements StrategyInterface {

    /**
     * @param AnalysisCollection $analysisCollection
     * @return string
     */
    public function filter(AnalysisCollection $analysisCollection)
    {
        $graph = $analysisCollection->getGraph();
        return json_encode($this->getVertexToVerticesArray($graph));
    }

    protected function getVertexToVerticesArray(Graph $graph) {
        $vertexToVertices = array();

        $vertices = $graph->getVertices();
        foreach($vertices as $from){
            $toVertices = $from->getVerticesEdgeTo();
                foreach($toVertices->getVerticesDistinct() as $to) {
                    $this->addArrayEdge($vertexToVertices, $from->getId(), $to->getId());
                }
        }

        return $vertexToVertices;
    }

    /**
     * @param $array array
     * @param $from string
     * @param $to string
     */
    protected function addArrayEdge(&$array, $from, $to) {
        if(array_key_exists($from, $array)) {
            $array[$from][] = $to;
        } else {
            $array[$from] = array($to);
        }
    }
}
