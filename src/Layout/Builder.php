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

use Fhaculty\Graph\Vertex;
use PhpDA\Entity\Adt;
use PhpDA\Entity\AnalysisCollection;
use PhpDA\Layout\Helper\GroupGenerator;
use PhpParser\Node\Name;

/**
 * @SuppressWarnings("PMD.CouplingBetweenObjects")
 */
class Builder implements BuilderInterface
{
    /** @var GraphViz */
    private $graphViz;

    /** @var GroupGenerator */
    private $groupGenerator;

    /** @var AnalysisCollection */
    private $analysisCollection;

    /** @var Vertex */
    private $adtRootVertex;

    /** @var LayoutInterface */
    private $layout;

    /** @var bool */
    private $isCallMode = false;

    /**
     * @param GraphViz       $graphViz
     * @param GroupGenerator $generator
     */
    public function __construct(GraphViz $graphViz, GroupGenerator $generator)
    {
        $this->graphViz = $graphViz;
        $this->groupGenerator = $generator;
        $this->setLayout(new NullLayout);
    }

    public function setAnalysisCollection(AnalysisCollection $collection)
    {
        $this->analysisCollection = $collection;
    }

    public function setCallMode()
    {
        $this->isCallMode = true;
    }

    /**
     * @param int $groupLength
     */
    public function setGroupLength($groupLength)
    {
        $this->groupGenerator->setGroupLength($groupLength);
    }

    /**
     * @param LayoutInterface $layout
     */
    public function setLayout(LayoutInterface $layout)
    {
        $this->layout = $layout;

    }

    public function getGraphViz()
    {
        return $this->graphViz;
    }

    public function create()
    {
        $this->createDependencies();
        $this->getGraph()->setLayout($this->layout->getGraph());
        $this->graphViz->setGroups($this->groupGenerator->getGroups());
        $this->graphViz->setGroupLayout($this->layout->getGroup());

        return $this;
    }

    private function createDependencies()
    {
        foreach ($this->analysisCollection->getAll() as $analysis) {
            foreach ($analysis->getAdts() as $adt) {
                $this->createVertexAndEdgesBy($adt);
            }
        }
    }

    /**
     * @param Adt $adt
     */
    private function createVertexAndEdgesBy(Adt $adt)
    {
        $this->createAdtRootVertexBy($adt);

        if ($this->isCallMode) {
            $this->createEdgesFor(
                $adt->getCalledNamespaces(),
                $this->layout->getEdge()
            );
        } else {
            $this->createEdgesFor(
                $adt->getMeta()->getImplementedNamespaces(),
                $this->layout->getEdgeImplement()
            );

            $this->createEdgesFor(
                $adt->getMeta()->getExtendedNamespaces(),
                $this->layout->getEdgeExtend()
            );

            $this->createEdgesFor(
                $adt->getMeta()->getUsedTraitNamespaces(),
                $this->layout->getEdgeTraitUse()
            );

            $this->createEdgesFor(
                $adt->getUsedNamespaces(),
                $this->layout->getEdge()
            );
        }

        $this->createEdgesFor(
            $adt->getUnsupportedStmts(),
            $this->layout->getEdgeUnsupported(),
            $this->layout->getVertexUnsupported()
        );

        $this->createEdgesFor(
            $adt->getNamespacedStrings(),
            $this->layout->getEdgeNamespacedString(),
            $this->layout->getVertexNamespacedString()
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
        $layout = $this->layout->getVertex();
        $vertex = $this->getGraph()->createVertex($name->toString(), true);

        if ($groupId = $this->groupGenerator->getIdFor($name)) {
            $vertex->setGroup($groupId);
            $layout['group'] = $groupId;
        }

        $vertex->setLayout($layout);

        return $vertex;
    }

    /**
     * @return Graph
     */
    private function getGraph()
    {
        return $this->graphViz->getGraph();
    }

    /**
     * @param Name[] $dependencies
     * @param array  $edgeLayout
     * @param array  $vertexLayout
     */
    private function createEdgesFor(array $dependencies, array $edgeLayout, array $vertexLayout = array())
    {
        foreach ($dependencies as $dependency) {
            $vertex = $this->createVertexBy($dependency);
            $vertex->setLayout(array_merge($vertex->getLayout(), $vertexLayout));
            if ($this->adtRootVertex !== $vertex
                && !$this->adtRootVertex->hasEdgeTo($vertex)
            ) {
                $this->adtRootVertex->createEdgeTo($vertex)->setLayout($edgeLayout);
            }
        }
    }
}
