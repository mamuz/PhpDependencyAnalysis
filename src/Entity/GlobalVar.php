<?php

namespace PhpDA\Entity;

class GlobalVar
{
    /** @var string */
    private $name;

    /** @var Type */
    private $type;

    /** @var RequestType */
    private $requestType;

    /** @var mixed */
    private $value;
}
