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

use PhpDA\Parser\Visitor\Required\AdtCollector;
use PhpDA\Parser\Visitor\Required\NameResolver;
use PhpDA\Plugin\FactoryInterface;
use PhpDA\Plugin\Loader;
use PhpParser\ParserFactory;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings("PMD.CouplingBetweenObjects")
 */
class AnalyzerFactory implements FactoryInterface
{
    /** @var LoggerInterface */
    private $logger;

    /**
     * @return Analyzer
     */
    public function create()
    {
        return new Analyzer(
            $this->createParser(),
            $this->createAdtTraverser(),
            $this->createNodeTraverser(),
            $this->getLogger()
        );
    }

    /**
     * @return \PhpParser\Parser
     */
    protected function createParser()
    {
        $factory = new ParserFactory();
        return $factory->create(ParserFactory::PREFER_PHP7);
    }

    /**
     * @return AdtTraverser
     */
    protected function createAdtTraverser()
    {
        $nameResolver = new NameResolver;
        $nameResolver->setLogger($this->getLogger());

        $traverser = new AdtTraverser;
        $traverser->bindNameResolver($nameResolver);
        $traverser->bindAdtCollector(new AdtCollector);

        return $traverser;
    }

    /**
     * @return NodeTraverser
     */
    protected function createNodeTraverser()
    {
        $loader = new Loader;
        $loader->setLogger($this->getLogger());

        $traverser = new NodeTraverser;
        $traverser->setVisitorLoader($loader);

        return $traverser;
    }

    /**
     * @return Logger
     */
    protected function getLogger()
    {
        if (!$this->logger instanceof LoggerInterface) {
            $this->logger = $this->createLogger();
        }

        return $this->logger;
    }

    /**
     * @return Logger
     */
    private function createLogger()
    {
        return new Logger;
    }
}
