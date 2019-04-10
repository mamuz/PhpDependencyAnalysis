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

namespace PhpDA\Parser;

use PhpDA\Entity\Adt;
use PhpDA\Entity\AdtAwareInterface;
use PhpDA\Plugin\LoaderInterface;
use PhpParser\NodeVisitor;

class NodeTraverser extends \PhpParser\NodeTraverser implements AdtAwareInterface
{
    /** @var Adt|null */
    private $adt;

    /** @var array */
    private $requiredVisitors = [];

    /** @var LoaderInterface */
    private $visitorLoader;

    /**
     * @param Adt $adt
     */
    public function setAdt(Adt $adt)
    {
        $this->adt = $adt;
    }

    /**
     * @return Adt|null
     */
    public function getAdt()
    {
        return $this->adt;
    }

    /**
     * @return bool
     */
    public function hasAdt()
    {
        return $this->adt instanceof Adt;
    }

    /**
     * @param LoaderInterface $visitorLoader
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

    /**
     * @param array $requiredVisitors
     * @return NodeTraverser
     */
    public function setRequiredVisitors(array $requiredVisitors)
    {
        $this->requiredVisitors = $requiredVisitors;
        return $this;
    }

    /**
     * @return array
     */
    public function getRequiredVisitors()
    {
        return $this->requiredVisitors;
    }

    public function bindVisitors(array $visitors, array $options = null)
    {
        $visitors = $this->filterVisitors($visitors);
        $options = $this->filterOptions($options);

        foreach ($visitors as $fqcn) {
            $visitorOptions = isset($options[$fqcn]) ? (array) $options[$fqcn] : null;
            $this->addVisitor($this->loadVisitorBy($fqcn, $visitorOptions));
        }
    }

    /**
     * @param array $visitors
     * @return array
     */
    private function filterVisitors(array $visitors)
    {
        $fqcns = $this->getRequiredVisitors();

        foreach ($visitors as $fqcn) {
            $fqcn = trim($fqcn, '\\');
            if (!in_array($fqcn, $fqcns)) {
                $fqcns[] = $fqcn;
            }
        }

        return $fqcns;
    }

    /**
     * @param array|null $options
     * @return array|null
     */
    private function filterOptions(array $options = null)
    {
        if (is_array($options)) {
            $filtered = [];
            foreach ($options as $key => $value) {
                $key = trim($key, '\\');
                $filtered[$key] = $value;
            }
            $options = $filtered;
        }

        return $options;
    }

    /**
     * @param string     $fqcn
     * @param array|null $options
     * @throws \RuntimeException
     * @return NodeVisitor
     */
    private function loadVisitorBy($fqcn, array $options = null)
    {
        $visitor = $this->getVisitorLoader()->get($fqcn, $options);

        if (!$visitor instanceof NodeVisitor) {
            throw new \RuntimeException(
                sprintf('Visitor \'%s\' must be an instance of PhpParser\\NodeVisitor', $fqcn)
            );
        }

        return $visitor;
    }

    public function traverse(array $nodes) : array
    {
        if ($this->hasAdt()) {
            foreach ($this->visitors as $visitor) {
                if ($visitor instanceof AdtAwareInterface) {
                    $visitor->setAdt($this->getAdt());
                }
            }
        }

        return parent::traverse($nodes);
    }
}
