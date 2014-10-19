<?php

namespace PhpDaTest\Stub;

use PhpDA\Writer\AdapterInterface as B;

/**
 * MyClass <please specify short description>
 *
 * @property PhpDaTest\Stub\FooBarProperty
 * @method PhpDaTest\Stub\FooBarMethod
 */
class MyClass
{
    /**
     * @param B[] $adapter
     * @return void
     */
    function myOtherFunc($adapter)
    {
        $adapter->to('any');
    }

    /**
     * @param $adapter
     * @return INTeger|$this
     */
    function myOther2Func($adapter)
    {
        /** @var \PhpDaTest\Stub\WriteAdapterFactory $adapter */
        $adapter->create();
    }
}
