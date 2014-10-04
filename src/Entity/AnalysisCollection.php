<?php

namespace PhpDA\Entity;

use Fhaculty\Graph\Graph;
use PhpParser\Node\Name;

class AnalysisCollection
{
    /** @var Graph */
    private $graph;

    /** @var Analysis[] */
    private $analyses = array();

    /**
     * @param Graph $graph
     */
    public function __construct(Graph $graph)
    {
        $this->graph = $graph;
    }

    /**
     * @return Graph
     */
    public function getGraph()
    {
        return $this->graph;
    }

    /**
     * @param Analysis $analysis
     * @param string   $filepath
     * @return void
     */
    public function attach(Analysis $analysis, $filepath)
    {
        $this->analyses[$filepath] = $analysis;

        $declaredNamespace = $this->createVertexBy($analysis->getDeclaredNamespace());

        foreach ($analysis->getUsedNamespaces() as $usedNamespace) {
            $usedNamespace = $this->createVertexBy($usedNamespace);
            if (!$usedNamespace->hasEdgeTo($declaredNamespace)) {
                $usedNamespace->createEdgeTo($declaredNamespace);
            }
        }
    }

    /**
     * @param Name $name
     * @return \Fhaculty\Graph\Vertex
     */
    private function createVertexBy(Name $name)
    {
        return $this->graph->createVertex($name->toString(), true);
    }
}
