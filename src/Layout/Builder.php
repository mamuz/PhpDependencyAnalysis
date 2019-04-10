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

use Fhaculty\Graph\Attribute\AttributeAware;
use Fhaculty\Graph\Edge\Directed;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use PhpDA\Entity\Adt;
use PhpDA\Entity\AnalysisCollection;
use PhpDA\Entity\Location;
use PhpDA\Layout\Helper\CycleDetector;
use PhpDA\Layout\Helper\GroupGenerator;
use PhpDA\Reference\ValidatorInterface;
use PhpParser\Node\Name;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @SuppressWarnings("PMD")
 */
class Builder implements BuilderInterface
{
    /** @var Graph */
    private $graph;

    /** @var boolean */
    private $graphHasViolations = false;

    /** @var GroupGenerator */
    private $groupGenerator;

    /** @var array */
    private $logEntries = [];

    /** @var CycleDetector */
    private $cycleDetector;

    /** @var AnalysisCollection */
    private $analysisCollection;

    /** @var Name */
    private $adtRootName;

    /** @var Vertex */
    private $adtRootVertex;

    /** @var LayoutInterface */
    private $layout;

    /** @var bool */
    private $isCallMode = false;

    /** @var SplFileInfo */
    private $currentAnalysisFile;

    /** @var ValidatorInterface */
    private $referenceValidator;

    /**
     * @param Graph          $graph
     * @param GroupGenerator $generator
     * @param CycleDetector  $cycleDetector
     */
    public function __construct(Graph $graph, GroupGenerator $generator, CycleDetector $cycleDetector)
    {
        $this->graph = $graph;
        $this->groupGenerator = $generator;
        $this->cycleDetector = $cycleDetector;

        $this->setLayout(new NullLayout);
        $this->setAnalysisCollection(new AnalysisCollection);
    }

    /**
     * @return Graph
     */
    public function getGraph()
    {
        return $this->graph;
    }

    public function hasViolations()
    {
        return $this->graphHasViolations;
    }

    public function setLogEntries(array $entries)
    {
        $this->logEntries = $entries;
    }

    public function setAnalysisCollection(AnalysisCollection $collection)
    {
        $this->analysisCollection = $collection;
    }

    public function setCallMode()
    {
        $this->isCallMode = true;
    }

    public function setReferenceValidator(ValidatorInterface $validator)
    {
        $this->referenceValidator = $validator;
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

    public function create()
    {
        $this->createDependencies();
        $this->detectCycles();
        $this->bindLayoutTo($this->getGraph(), $this->layout->getGraph(), 'graphviz.graph.');
        $this->getGraph()->setAttribute('graphviz.groups', $this->groupGenerator->getGroups());
        $this->getGraph()->setAttribute('graphviz.groupLayout', $this->layout->getGroup());
        $this->getGraph()->setAttribute('logEntries', $this->logEntries);

        return $this;
    }

    /**
     * @param AttributeAware $attributeAware
     * @param array          $layout
     * @param string         $prefix
     */
    private function bindLayoutTo(AttributeAware $attributeAware, array $layout, $prefix = 'graphviz.')
    {
        foreach ($layout as $name => $attr) {
            $attributeAware->setAttribute($prefix . $name, $attr);
        }
    }

    private function createDependencies()
    {
        foreach ($this->analysisCollection->getAll() as $analysis) {
            $this->currentAnalysisFile = $analysis->getFile();
            foreach ($analysis->getAdts() as $adt) {
                if (!$adt->hasDeclaredGlobalNamespace()) {
                    $this->createVertexAndEdgesBy($adt);
                }
            }
        }
    }

    private function detectCycles()
    {
        $cycledEdges = $this->cycleDetector->inspect($this->getGraph())->getCycledEdges();

        foreach ($cycledEdges as $edge) {
            /** @var Directed $edge */
            $edge->setAttribute('belongsToCycle', true);
            if (!$edge->getAttribute('referenceValidatorMessages')) {
                $this->bindLayoutTo($edge, $this->layout->getEdgeCycle());
            }
            $this->graphHasViolations = true;
        }

        $this->getGraph()->setAttribute('cycles', $this->cycleDetector->getCycles());
    }

    /**
     * @param Adt $adt
     */
    private function createVertexAndEdgesBy(Adt $adt)
    {
        $this->adtRootName = $adt->getDeclaredNamespace();
        $this->adtRootVertex = $this->createVertexBy($this->adtRootName);
        $location = new Location($this->currentAnalysisFile, $this->adtRootName);
        $this->addLocationTo($this->adtRootVertex, $location);
        $this->adtRootVertex->setAttribute('adt', $adt->toArray());

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
     * @param Name $name
     * @return Vertex
     */
    private function createVertexBy(Name $name)
    {
        $vertex = $this->getGraph()->createVertex($name->toString(), true);

        if ($groupId = $this->groupGenerator->getIdFor($name)) {
            $vertex->setGroup($groupId);
            $vertex->setAttribute('graphviz.group', $groupId);
        }

        $this->bindLayoutTo($vertex, $this->layout->getVertex());

        return $vertex;
    }

    /**
     * @param Name[] $dependencies
     * @param array  $edgeLayout
     * @param array  $vertexLayout
     */
    private function createEdgesFor(array $dependencies, array $edgeLayout, array $vertexLayout = [])
    {
        foreach ($dependencies as $dependency) {
            $vertex = $this->createVertexBy($dependency);
            $this->bindLayoutTo($vertex, $vertexLayout);
            if ($this->adtRootVertex !== $vertex) {
                $edge = $this->createEdgeToAdtRootVertexBy($vertex);
                $this->bindLayoutTo($edge, $edgeLayout);
                $this->validateDependency($dependency, $edge);
                $location = new Location($this->currentAnalysisFile, $dependency);
                $this->addLocationTo($edge, $location);
            }
        }
    }

    /**
     * @param Name           $dependency
     * @param AttributeAware $edge
     */
    private function validateDependency(Name $dependency, AttributeAware $edge)
    {
        if ($this->referenceValidator
            && !$this->referenceValidator->isValidBetween(clone $this->adtRootName, clone $dependency)
        ) {
            $this->graphHasViolations = true;
            $this->bindLayoutTo($edge, $this->layout->getEdgeInvalid());
            $edge->setAttribute('referenceValidatorMessages', $this->referenceValidator->getMessages());
        }
    }

    /**
     * @param Vertex $vertex
     * @return \Fhaculty\Graph\Edge\Directed
     */
    private function createEdgeToAdtRootVertexBy(Vertex $vertex)
    {
        foreach ($this->adtRootVertex->getEdges() as $edge) {
            /** @var \Fhaculty\Graph\Edge\Directed $edge */
            if ($edge->isConnection($this->adtRootVertex, $vertex)) {
                return $edge;
            }
        }

        return $this->adtRootVertex->createEdgeTo($vertex);
    }

    /**
     * @param AttributeAware $attributeAware
     * @param Location       $location
     */
    private function addLocationTo(AttributeAware $attributeAware, Location $location)
    {
        $locations = $attributeAware->getAttribute('locations', []);

        $key = base64_encode(serialize($location->toArray()));
        $locations[$key] = $location;

        $attributeAware->setAttribute('locations', $locations);
    }
}
