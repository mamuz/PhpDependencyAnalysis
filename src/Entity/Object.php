<?php

namespace PhpDA\Entity;

class Object
{
    const TYPE_CONCRET = 0;

    const TYPE_ABSTRACT = 1;

    const TYPE_INTERFACE = 2;

    const TYPE_TRAIT = 3;

    const TYPE_FINAL = 3;

    /** @var string */
    private $fqn;

    /** @var Doc */
    private $doc;

    /** @var int */
    private $type;

    /** @var Object[] */
    private $parents;

    /** @var Object[] */
    private $contracts;

    /** @var Constant[] */
    private $constants;

    /** @var Property[] */
    private $properties;

    /** @var Method[] */
    private $methods;
}
