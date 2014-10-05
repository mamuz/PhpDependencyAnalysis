<?php

namespace PhpDA\Writer\Strategy;

class Text extends AbstractStrategy
{
    public function createOutput()
    {
        return $this->getGraphViz()->createScript();
    }
}
