<?php

namespace PhpDA\Parser;

use PhpParser\Node;

interface TraverseInterface
{
    /**
     * @param array $visitors
     * @return void
     */
    public function bindVisitors(array $visitors);
}
