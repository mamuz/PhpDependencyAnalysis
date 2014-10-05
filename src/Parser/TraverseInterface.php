<?php

namespace PhpDA\Parser;

use PhpParser\Node;

interface TraverseInterface
{
    /**
     * @param array      $visitors
     * @param array|null $options
     * @return mixed
     */
    public function bindVisitors(array $visitors, array $options = null);
}
