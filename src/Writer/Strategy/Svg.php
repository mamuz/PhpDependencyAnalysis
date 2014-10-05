<?php

namespace PhpDA\Writer\Strategy;

class Svg extends AbstractStrategy
{
    public function createOutput()
    {
        return $this->getGraphViz()->setFormat('svg')->createImageData();
    }
}
