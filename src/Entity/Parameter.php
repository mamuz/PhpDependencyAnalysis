<?php

namespace PhpDA\Entity;

class Parameter
{
    /** @var string */
    private $name;

    /** @var bool */
    private $hasTypeHint = false;

    /** @var Type */
    private $type;

    /** @var mixed */
    private $value;
}
