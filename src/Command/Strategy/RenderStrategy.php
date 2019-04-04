<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2017 Marco Muths
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

namespace PhpDA\Command\Strategy;

use Fhaculty\Graph\Edge\Directed;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use PhpDA\Command\Config;
use PhpDA\Command\MessageInterface as Message;
use PhpDA\Entity\Cycle;
use PhpDA\Entity\Location;
use PhpDA\Layout;
use PhpDA\Plugin\ConfigurableInterface;
use PhpDA\Plugin\LoaderInterface;
use PhpDA\Writer\AdapterInterface;
use PhpParser\Node\Name;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @SuppressWarnings("PMD.CouplingBetweenObjects")
 */
class RenderStrategy implements ConfigurableInterface, StrategyInterface
{
    /** @var Config */
    private $config;

    /** @var OutputInterface */
    private $output;

    /** @var string */
    private $source;

    /** @var string */
    private $target;

    /** @var AdapterInterface */
    private $writeAdapter;

    /**
     * @param Layout\BuilderInterface $graphBuilder
     * @param AdapterInterface        $writeAdapter
     * @param LoaderInterface         $loader
     */
    public function __construct(
        AdapterInterface $writeAdapter
    ) {
        $this->writeAdapter = $writeAdapter;

        $this->config = new Config([]);
        $this->output = new NullOutput;
    }

    public function setOptions(array $options)
    {
        if (isset($options['config']) && $options['config'] instanceof Config) {
            $this->config = $options['config'];
        }

        if (isset($options['output']) && $options['output'] instanceof OutputInterface) {
            $this->output = $options['output'];
        }

        $this->target = $options['target'];
        $this->source = $options['source'];
    }

    /**
     * @return Config
     */
    protected function getConfig()
    {
        return $this->config;
    }

    /**
     * @return OutputInterface
     */
    protected function getOutput()
    {
        return $this->output;
    }

    /**
     * @return AdapterInterface
     */
    protected function getWriteAdapter()
    {
        return $this->writeAdapter;
    }

    public function execute()
    {
        $graph = $this->loadGraph();
        $this->writeGraph($graph);
        $this->getOutput()->writeln(PHP_EOL . Message::DONE . PHP_EOL);

        return true;
    }

    private function writeGraph( Graph $graph )
    {
        $this->getOutput()->writeln(
            PHP_EOL . PHP_EOL . sprintf(Message::WRITE_GRAPH_TO, $this->target)
        );

        $this->getWriteAdapter()
             ->write($graph)
             ->with($this->getConfig()->getFormatter())
             ->to($this->target);
    }

    /**
     * @return \Fhaculty\Graph\Graph
     */
    private function loadGraph()
    {
        $json = file_get_contents( $this->source );
        $data = json_decode( $json, JSON_OBJECT_AS_ARRAY );

        // Refer to PhpDA\Writer\Extractor\Graph::extract for the array structure.

        $graph = new Graph();
        $this->makeVertices($graph, $data['vertices']);
        $this->makeEdges($graph, $data['edges']);

        $graph->setAttribute('cycles', $this->makeCycles($data['cycles']));
        $graph->setAttribute('graphviz.groups', $data['groups']);

        $graph->setAttribute('logEntries', $data['log']);
        $graph->setAttribute('graphviz.graph.label', $data['label']);

        return $graph;
    }

    private function makeCycles( array $list ) {
        $cycles = [];
        foreach ( $list as $row ) {
            $cycles[] = new Cycle( $row );
        }
        return $cycles;
    }

    /**
     * @param Graph $graph
     * @param array[] $list
     *
     * @return Directed[]
     */
    private function makeEdges( Graph $graph, array $list ) {
        $edges = [];
        foreach ( $list as $row ) {
            // Refer to PhpDA\Writer\Extractor\Graph::extractEdge for the array structure.
            $from = $graph->getVertex( $row['from'] );
            $to = $graph->getVertex( $row['to'] );
            $e = new Directed( $from, $to );
            $e->setAttribute( 'locations', $this->makeLocations( $row['locations'] ) );
            $e->setAttribute( 'belongsToCycle', $row['belongsToCycle'] );
            $e->setAttribute( 'referenceValidatorMessages', $row['referenceValidatorMessages'] );
            $edges[] = $e;
        }

        return $edges;
    }

    /**
     * @param Graph $graph
     * @param array[] $list
     *
     * @return Vertex[]
     */
    private function makeVertices( Graph $graph, array $list ) {
        $vertices = [];
        foreach ( $list as $row ) {
            // Refer to PhpDA\Writer\Extractor\Graph::extractVertex for the array structure.
            $v = new Vertex( $graph, $row['name'] );
            $v->setAttribute( 'adt', $row['adt'] );
            $v->setAttribute( 'locations', $row['location'] ? $this->makeLocations( [ $row['location'] ] ) : [] );
            $v->setGroup( $row['group'] );
            $vertices[] = $v;
        }

        return $vertices;
    }

    /**
     * @param array[] $data
     *
     * @return Location[]
     */
    private function makeLocations( array $list ) {
        $locations = [];
        foreach ( $list as $row ) {
            $file = new SplFileInfo( $row[ 'file' ], $row[ 'file' ], '' );
            $name = new Name( $row[ 'file' ], $row );
            $locations[] = new Location( $file, $name );
        }
        return $locations;
    }

}
