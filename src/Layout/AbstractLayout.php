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

abstract class AbstractLayout implements LayoutInterface
{
    /** @var array */
    private $vertex = array(
        'fillcolor' => '#eeeeee',
        'style'     => 'filled',
        'shape'     => 'circle',
        'fontcolor' => '#314B5F',
        'fontsize'  => 10,
        'margin'    => 0,
    );

    /** @var array */
    private $vertexUnsupported = array(
        'fillcolor' => '#ECB4B4',
    );

    /** @var array */
    private $vertexNamespacedString = array(
        'fillcolor' => '#F1EEA6',
    );

    /** @var array */
    private $edge = array(
        'arrowsize' => 0.6,
        'fontcolor' => '#767676',
        'fontsize'  => 8,
        'color'     => '#1A2833',
    );

    /** @var array */
    private $edgeUnsupported = array(
        'color' => '#ECB4B4',
    );

    /** @var array */
    private $edgeNamespacedString = array(
        'color' => '#F1EEA6',
    );

    public function getEdge()
    {
        return $this->edge;
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
