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

namespace PhpDA\Writer\Layout;

class Aggregation implements LayoutInterface
{
    /** @var array */
    private $vertex = array(
        'fillcolor' => '#eeeeee',
        'style'     => 'filled, rounded',
        'shape'     => 'box',
        'fontcolor' => '#314B5F',
    );

    /** @var array */
    private $vertexClass = array();

    /** @var array */
    private $vertexInterface = array();

    /** @var array */
    private $vertexAbstract = array();

    /** @var array */
    private $vertexFinal = array();

    /** @var array */
    private $vertexTrait = array();

    /** @var array */
    private $vertexUnsupported = array();

    /** @var array */
    private $vertexNamespacedString = array();

    /** @var array */
    private $edge = array(
        'fontcolor' => '#767676',
        'fontsize'  => 10,
        'color'     => '#1A2833',
    );

    /** @var array */
    private $edgeImplement = array(
        'style'     => 'dashed',
        'arrowType' => 'empty',
    );

    /** @var array */
    private $edgeExtend = array(
        'style'     => 'solid',
        'arrowType' => 'empty',
    );

    /** @var array */
    private $edgeTraitUse = array(
        'style'     => 'solid',
        'arrowType' => 'empty',
    );

    public function getEdge()
    {
        return $this->edge;
    }

    public function getEdgeExtend()
    {
        return $this->edgeExtend + $this->getEdge();
    }

    public function getEdgeImplement()
    {
        return $this->edgeImplement + $this->getEdge();
    }

    public function getEdgeTraitUse()
    {
        return $this->edgeTraitUse + $this->getEdge();
    }

    public function getVertex()
    {
        return $this->vertex;
    }

    public function getVertexAbstract()
    {
        return $this->vertexAbstract + $this->getVertex();
    }

    public function getVertexClass()
    {
        return $this->vertexClass + $this->getVertex();
    }

    public function getVertexFinal()
    {
        return $this->vertexFinal + $this->getVertex();
    }

    public function getVertexInterface()
    {
        return $this->vertexInterface + $this->getVertex();
    }

    public function getVertexNamespacedString()
    {
        return $this->vertexNamespacedString + $this->getVertex();
    }

    public function getVertexTrait()
    {
        return $this->vertexTrait + $this->getVertex();
    }

    public function getVertexUnsupported()
    {
        return $this->vertexUnsupported + $this->getVertex();
    }
}
