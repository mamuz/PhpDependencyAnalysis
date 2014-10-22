<?php

namespace PhpDaTest\Stub;

use PhpDA\Writer\AdapterInterface as B;

/**
 * MyClass <please specify short description>
 *
 * @Route("/")
 * @property PhpDaTest\Stub\FooBarProperty
 * @property PhpDaTest\Stub\FooBarProperty2
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
     * @deprecated
     * @Entity
     * @Param $uppercased
     * @param $adapter
     * @return INTeger|$this
     */
    function myOther2Func($adapter)
    {
        /** @var \PhpDaTest\Stub\WriteAdapterFactory $adapter */
        $adapter->create();
    }
}
