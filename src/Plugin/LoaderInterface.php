<?php

namespace PhpDA\Plugin;

interface LoaderInterface
{
    /**
     * @param string     $fqn
     * @param array|null $options
     * @throws \RuntimeException
     * @return object
     */
    public function get($fqn, array $options = null);
}
