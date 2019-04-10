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

namespace PhpDA\Plugin;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class Loader implements LoaderInterface, LoggerAwareInterface
{
    /** @var LoggerInterface */
    private $logger;

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function get($fqcn, array $options = null)
    {
        $this->validateClassExistence($fqcn);
        $this->validateConstructorArgumentExistence($fqcn);

        $plugin = new $fqcn;

        if ($plugin instanceof FactoryInterface) {
            $plugin = $plugin->create();
        }

        $this->tryLoggerBinding($plugin);

        if (is_array($options)) {
            $this->tryConfigWith($options, $plugin);
        }

        return $plugin;
    }

    /**
     * @param string $fqcn
     * @throws \RuntimeException
     */
    private function validateClassExistence($fqcn)
    {
        if (!class_exists($fqcn)) {
            throw new \RuntimeException(sprintf('Class for \'%s\' does not exist', $fqcn));
        }
    }

    /**
     * @param string $fqcn
     * @throws \RuntimeException
     */
    private function validateConstructorArgumentExistence($fqcn)
    {
        $class = new \ReflectionClass($fqcn);

        if ($constructor = $class->getConstructor()) {
            if ($constructor->getNumberOfParameters()) {
                throw new \RuntimeException(sprintf('Class \'%s\' must be creatable without arguments', $fqcn));
            }
        }
    }

    /**
     * @param object $plugin
     */
    private function tryLoggerBinding($plugin)
    {
        if ($this->logger && $plugin instanceof LoggerAwareInterface) {
            $plugin->setLogger($this->logger);
        }
    }

    /**
     * @param array  $options
     * @param object $plugin
     */
    private function tryConfigWith(array $options, $plugin)
    {
        if ($plugin instanceof ConfigurableInterface) {
            $plugin->setOptions($options);
        }
    }
}
