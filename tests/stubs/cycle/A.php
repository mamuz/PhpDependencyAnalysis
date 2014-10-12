<?php

namespace PhpDaTest\Stub\A;

class A
{
    public function __construct()
    {
        new \PhpDaTest\Stub\B\B;
    }
}
