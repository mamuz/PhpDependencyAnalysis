<?php

namespace PackageX;

class UnsupportedStmt
{
    public function __construct()
    {
        global $db, $user;

        $str = 'foo';
        eval ("\$str = \"$str\"; new DateTime;");
        call_user_func($str);
        call_user_func_array($str, array());
        forward_static_call($str);
        forward_static_call_array($str);
        call_user_method($str, $str);
        call_user_method_array($str, $str, array());
        create_function($str, $str);
    }
}

class UnsupportedVar1
{
    public function __construct()
    {
        $var = 'baz';
        $$var;
    }
}

class UnsupportedVar2
{
    public function __construct()
    {
        $staticMethod = 'baz';
        $staticClass = new \Locale();
        $staticClass::$staticMethod();
    }
}

class UnsupportedVar3
{
    public function __construct()
    {
        $func = 'baz';
        $func();
    }
}

class UnsupportedVar4
{
    public function __construct()
    {
        $var = 'baz';
        new $var();
    }
}

class UnsupportedVar5
{
    public function __construct()
    {
        $var = 'baz';
        new $var;
    }
}
