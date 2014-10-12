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

namespace PhpDA\Parser;

use PhpDA\Entity\AnalysisAwareInterface;
use PhpDA\Entity\AnalysisAwareTrait;
use PhpDA\Plugin\LoaderInterface;
use PhpParser\NodeVisitor;

class NodeTraverser extends \PhpParser\NodeTraverser implements AnalysisAwareInterface
{
    use AnalysisAwareTrait;

    /** @var array */
    private $requiredVisitors = array(
        'PhpDA\Parser\Visitor\Required\MultiNamespaceDetector',
        'PhpParser\NodeVisitor\NameResolver',
        'PhpDA\Parser\Visitor\Required\DeclaredNamespaceCollector',
        'PhpDA\Parser\Visitor\Required\UsedNamespaceCollector',
    );

    /** @var LoaderInterface */
    private $visitorLoader;

    /**
     * @param LoaderInterface $visitorLoader
     * @return void
     */
    public function setVisitorLoader(LoaderInterface $visitorLoader)
    {
        $this->visitorLoader = $visitorLoader;
    }

    /**
     * @return LoaderInterface
     * @throws \DomainException
     */
    public function getVisitorLoader()
    {
        if (!$this->visitorLoader instanceof LoaderInterface) {
            throw new \DomainException('VisitorLoader has not been set');
        }

        return $this->visitorLoader;
    }

    public function bindVisitors(array $visitors, array $options = null)
    {
        $visitors = $this->filterVisitors($visitors);
        $options = $this->filterOptions($options);

        foreach ($visitors as $fqn) {
            $visitorOptions = isset($options[$fqn]) ? (array) $options[$fqn] : null;
            $this->addVisitor($this->loadVisitorBy($fqn, $visitorOptions));
        }
    }

    /**
     * @param array $visitors
     * @return array
     */
    private function filterVisitors(array $visitors)
    {
        $fqns = $this->requiredVisitors;

        foreach ($visitors as $fqn) {
            $fqn = trim($fqn, '\\');
            if (!in_array($fqn, $fqns)) {
                $fqns[] = $fqn;
            }
        }

        return $fqns;
    }

    /**
     * @param array|null $options
     * @return array|null
     */
    private function filterOptions(array $options = null)
    {
        if (is_array($options)) {
            $filtered = array();
            foreach ($options as $key => $value) {
                $key = trim($key, '\\');
                $filtered[$key] = $value;
            }
            $options = $filtered;
        }

        return $options;
    }

    /**
     * @param string     $fqn
     * @param array|null $options
     * @throws \RuntimeException
     * @return NodeVisitor
     */
    private function loadVisitorBy($fqn, array $options = null)
    {
        $visitor = $this->getVisitorLoader()->get($fqn, $options);

        if (!$visitor instanceof NodeVisitor) {
            throw new \RuntimeException('Visitor ' . $fqn . ' must be an instance of NodeVisitor');
        }

        return $visitor;
    }

    public function traverse(array $nodes)
    {
        foreach ($this->visitors as $visitor) {
            if ($visitor instanceof AnalysisAwareInterface) {
                $visitor->setAnalysis($this->getAnalysis());
            }
        }

        return parent::traverse($nodes);
    }
}
