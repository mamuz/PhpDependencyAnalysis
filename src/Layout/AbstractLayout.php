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

namespace PhpDA\Layout;

abstract class AbstractLayout implements LayoutInterface
{
    /** @var array */
    private $graph = [
        'rankdir'  => 'LR',
        'ranksep'  => 1,
        'nodesep'  => 0.1,
        'fontsize' => 8,
    ];

    /** @var array */
    private $group = [
        'style'     => 'rounded,filled',
        'fontcolor' => '#000033',
        'fontsize'  => 14,
        'labeljust' => 'l',
        'color'     => '#000033',
        'fillcolor' => '#CCCCFF',
    ];

    /** @var array */
    private $vertex = [
        'fillcolor' => '#9999CC',
        'style'     => 'filled,rounded',
        'shape'     => 'box',
        'fontcolor' => '#000033',
        'fontsize'  => 10,
    ];

    /** @var array */
    private $vertexUnsupported = [
        'fillcolor' => '#FF9999',
    ];

    /** @var array */
    private $vertexNamespacedString = [
        'fillcolor' => '#FFCC66',
    ];

    /** @var array */
    private $edge = [
        'arrowsize' => 0.6,
        'fontcolor' => '#999999',
        'fontsize'  => 8,
        'color'     => '#999999',
        'weight'    => 1.2,
    ];

    /** @var array */
    private $edgeInvalid = [
        'color'     => '#FF0000',
        'style'     => 'bold',
        'arrowsize' => 0.8,
    ];

    /** @var array */
    private $edgeCycle = [
        'color'     => '#FF0099',
        'style'     => 'bold',
        'arrowsize' => 0.8,
    ];

    /** @var array */
    private $edgeUnsupported = [
        'color' => '#FF9999',
    ];

    /** @var array */
    private $edgeNamespacedString = [
        'color' => '#FFCC66',
    ];

    public function __construct($label)
    {
        $this->graph['label'] = $label;
    }

    public function getGraph()
    {
        return $this->graph;
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function getEdge()
    {
        return $this->edge;
    }

    public function getEdgeInvalid()
    {
        return $this->edgeInvalid;
    }

    public function getEdgeCycle()
    {
        return $this->edgeCycle;
    }

    public function getEdgeExtend()
    {
        return $this->getEdge();
    }

    public function getEdgeImplement()
    {
        return $this->getEdge();
    }

    public function getEdgeTraitUse()
    {
        return $this->getEdge();
    }

    public function getEdgeUnsupported()
    {
        return $this->edgeUnsupported + $this->getEdge();
    }

    public function getEdgeNamespacedString()
    {
        return $this->edgeNamespacedString + $this->getEdge();
    }

    public function getVertex()
    {
        return $this->vertex;
    }

    public function getVertexNamespacedString()
    {
        return $this->vertexNamespacedString + $this->getVertex();
    }

    public function getVertexUnsupported()
    {
        return $this->vertexUnsupported + $this->getVertex();
    }
}
