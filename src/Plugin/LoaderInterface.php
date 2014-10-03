<?php

namespace PhpDA\Plugin;

interface LoaderInterface
{
    /**
     * @param string $fqn
     * @throws \RuntimeException
     * @return object
     */
    public function get($fqn);
}
