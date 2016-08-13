<?php

namespace PackageX;

class NamespacedString
{
    public function __construct()
    {
        $nameSpace1 = 'Test\Object';
        $nameSpace2 = '\Test';
        $nameSpace3 = 'noNamespace';

        $class = new \stdClass();

        $class->get();
        $class->get('foo_bar', array());
        $class->get($nameSpace1);
    }
}
