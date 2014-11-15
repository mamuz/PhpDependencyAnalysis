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

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use PhpDA\Layout;
use PhpParser\Error;
use PhpParser\Node\Name;

class AnalysisCollection
{
    /** @var Graph */
    private $graph;

    /** @var Vertex */
    private $adtRootVertex;

    /** @var Error[] */
    private $analysisFailures = array();

    /** @var Layout\LayoutInterface */
    private $layout;

    /** @var bool */
    private $isCallMode = false;

    /**
     * @param Graph $graph
     */
    public function __construct(Graph $graph)
    {
        $this->graph = $graph;
        $this->setLayout(new Layout\NullLayout);
    }

    /**
     * @return Graph
     */
    public function getGraph()
    {
        return $this->graph;
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
     * @param Layout\LayoutInterface $layout
     */
    public function setLayout(Layout\LayoutInterface $layout)
    {
        $this->layout = $layout;
    }

    /**
     * @return Layout\LayoutInterface
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @return bool
     */
    public function hasAnalysisFailures()
    {
        return !empty($this->analysisFailures);
    }

    /**
     * @return Error[]
     */
    public function getAnalysisFailures()
    {
        return $this->analysisFailures;
    }

    /**
     * @param Analysis $analysis
     */
    public function attach(Analysis $analysis)
    {
        if ($analysis->hasParseError()) {
            $this->addAnalysisFailure($analysis);
        } else {
            foreach ($analysis->getAdts() as $adt) {
                $this->attachAdt($adt);
            }
        }
    }

    /**
     * @param Analysis $analysis
     */
    private function addAnalysisFailure(Analysis $analysis)
    {
        $this->analysisFailures[$analysis->getFile()->getRealPath()] = $analysis->getParseError();
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
        return $this->graph->createVertex($name->toString(), true)->setLayout($this->getLayout()->getVertex());
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
