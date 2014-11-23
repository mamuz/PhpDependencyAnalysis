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

namespace PhpDA\Entity;

use Fhaculty\Graph\Vertex;
use PhpDA\Layout;
use PhpParser\Node\Name;

class AnalysisCollection
{
    /** @var Layout\Graph */
    private $graph;

    /** @var Vertex */
    private $adtRootVertex;

    /** @var Layout\LayoutInterface */
    private $layout;

    /** @var bool */
    private $isCallMode = false;

    /** @var array */
    private $groups = array();

    /** @var int */
    private $groupLength = 0;

    /**
     * @param Layout\Graph $graph
     */
    public function __construct(Layout\Graph $graph)
    {
        $this->graph = $graph;
        $this->bindLayout(new Layout\NullLayout);
    }

    /**
     * @return Layout\Graph
     */
    public function getGraph()
    {
        return $this->graph;
    }

    public function getGroups()
    {
        return $this->groups;
    }

    public function setCallMode()
    {
        $this->isCallMode = true;
    }

    /**
     * @return bool
     */
    public function isCallMode()
    {
        return $this->isCallMode;
    }

    /**
     * @param int $groupLength
     * @return AnalysisCollection
     */
    public function setGroupLength($groupLength)
    {
        $this->groupLength = (int) $groupLength;
        return $this;
    }

    /**
     * @return int
     */
    public function getGroupLength()
    {
        return $this->groupLength;
    }

    /**
     * @param Layout\LayoutInterface $layout
     */
    public function bindLayout(Layout\LayoutInterface $layout)
    {
        $this->layout = $layout;
        $this->getGraph()->setLayout($this->layout->getGraph());
    }

    /**
     * @return Layout\LayoutInterface
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param Analysis $analysis
     */
    public function attach(Analysis $analysis)
    {
        foreach ($analysis->getAdts() as $adt) {
            $this->attachAdt($adt);
        }
    }

    /**
     * @param Adt $adt
     */
    private function attachAdt(Adt $adt)
    {
        $this->createAdtRootVertexBy($adt);

        if ($this->isCallMode()) {
            $this->createEdgesFor(
                $adt->getCalledNamespaces(),
                $this->getLayout()->getEdge()
            );
        } else {
            $this->createEdgesFor(
                $adt->getMeta()->getImplementedNamespaces(),
                $this->getLayout()->getEdgeImplement()
            );

            $this->createEdgesFor(
                $adt->getMeta()->getExtendedNamespaces(),
                $this->getLayout()->getEdgeExtend()
            );

            $this->createEdgesFor(
                $adt->getMeta()->getUsedTraitNamespaces(),
                $this->getLayout()->getEdgeTraitUse()
            );

            $this->createEdgesFor(
                $adt->getUsedNamespaces(),
                $this->getLayout()->getEdge()
            );
        }

        $this->createEdgesFor(
            $adt->getUnsupportedStmts(),
            $this->getLayout()->getEdgeUnsupported(),
            $this->getLayout()->getVertexUnsupported()
        );

        $this->createEdgesFor(
            $adt->getNamespacedStrings(),
            $this->getLayout()->getEdgeNamespacedString(),
            $this->getLayout()->getVertexNamespacedString()
        );
    }

    /**
     * @param Adt $adt
     */
    private function createAdtRootVertexBy(Adt $adt)
    {
        $this->adtRootVertex = $this->createVertexBy($adt->getDeclaredNamespace());
    }

    /**
     * @param Name $name
     * @return Vertex
     */
    private function createVertexBy(Name $name)
    {
        $vertex = $this->graph->createVertex($name->toString(), true);

        if ($this->getGroupLength() > 0) {
            $vertex->setGroup($this->generateGroupIdBy($name));
        }

        $layout = $this->getLayout()->getVertex();
        $vertex->setLayout($layout);

        return $vertex;
    }

    /**
     * @param Name $name
     * @return int
     */
    private function generateGroupIdBy(Name $name)
    {
        $group = implode('\\', array_slice($name->parts, 0, $this->getGroupLength()));

        if (!in_array($group, $this->groups)) {
            $id = (sizeof($this->groups) + 1) * -1;
            $this->groups[$id] = $group;
            return $id;
        }

        return array_search($group, $this->groups);
    }

    /**
     * @param Name[] $dependencies
     * @param array  $edgeLayout
     * @param array  $vertexLayout
     */
    private function createEdgesFor(array $dependencies, array $edgeLayout, array $vertexLayout = null)
    {
        foreach ($dependencies as $dependency) {
            $vertex = $this->createVertexBy($dependency);
            if (is_array($vertexLayout)) {
                $vertex->setLayout($vertexLayout);
            }
            if ($this->adtRootVertex !== $vertex
                && !$this->adtRootVertex->hasEdgeTo($vertex)
            ) {
                $this->adtRootVertex->createEdgeTo($vertex)->setLayout($edgeLayout);
            }
        }
    }
}
