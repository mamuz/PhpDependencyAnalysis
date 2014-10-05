<?php

namespace PhpDA\Parser;

interface TraverseInterface
{
    /**
     * @param array      $visitors
     * @param array|null $options
     * @return mixed
     */
    public function bindVisitors(array $visitors, array $options = null);
}
