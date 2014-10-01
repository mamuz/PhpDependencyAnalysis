<?php

namespace PhpDA\Parser\Visitor;

use PhpDA\Entity\AnalysisAwareInterface;
use PhpDA\Entity\AnalysisAwareTrait;
use PhpParser\NodeVisitorAbstract;

abstract class AbstractVisitor extends NodeVisitorAbstract implements AnalysisAwareInterface
{
    use AnalysisAwareTrait;
}
