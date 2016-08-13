<?php

namespace PackageX;

class Constant
{
    const FOO = 234;
    const BAR = 432;

    public function __construct()
    {
        $foo = static::FOO;
        $bar = self::BAR;
        __CLASS__;
        PHP_EOL;
        \Locale::ACTUAL_LOCALE;
    }
}
