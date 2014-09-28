<?php

namespace PhpDA\Entity;

class Property
{
    /** @var string */
    private $name;

    /** @var Doc */
    private $doc;

    /** @var bool */
    private $isStatic = false;

    /** @var Type */
    private $type;

    /** @var Access */
    private $access;

    /** @var mixed */
    private $value;
}
