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

namespace PhpDA\Layout;

use Fhaculty\Graph\Graph as FhacultyGraph;

/**
 * @method \PhpDA\Layout\Helper\VertexProxy createVertex(int $id = null, bool $returnDuplicate = false)
 * @method \PhpDA\Layout\Helper\VertexProxy getVertex($id)
 * @method \PhpDA\Layout\Helper\VertexProxy[] | \Fhaculty\Graph\Set\Vertices getVertices()
 * @method \PhpDA\Layout\Helper\EdgeProxy[] | \Fhaculty\Graph\Set\Edges getEdges()
 */
class Graph extends FhacultyGraph
{
    /** @var array */
    private $layout = array();

    /**
     * @param array $layout
     */
    public function setLayout(array $layout)
    {
        $this->layout = $layout;
    }

    /**
     * @return array
     */
    public function getLayout()
    {
        return $this->layout;
    }
}
