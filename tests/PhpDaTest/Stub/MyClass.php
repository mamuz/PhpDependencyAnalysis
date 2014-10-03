<?php

namespace PhpDaTest\Stub;

use PhpDA\Entity\AnalysisAwareInterface as B;
use PhpDA\Entity\AnalysisAwareTrait;
use PhpDA\Entity\AnalysisCollection;
use PhpDA\Service\AnalyzerFactory;
use PhpDA\Service\WriteAdapterFactory;
use PhpDA\Writer\AdapterInterface;

class MyClass extends \SplObjectStorage implements B
{
    use AnalysisAwareTrait;

    public function __construct()
    {
        $test = new AnalysisCollection();
    }
}

class MyClassExt extends \SplObjectStorage implements B
{
    use AnalysisAwareTrait;

    private $bar = 0;

    const ANY = 234;

    public function __construct()
    {
        $test = new AnalysisCollection();
        $foo = static::ANY;
        $foo = self::ANY;
        $foo = $this->bar;
        __CLASS__;
    }
}

$test = new AnalyzerFactory(new WriteAdapterFactory());
$post = $_POST;
$post = $_GET;
$post = $_REQUEST;
$post = $_COOKIE;
$post = $_FILES;
$post = $_SERVER;
$post = $_ENV;
$post = $GLOBALS;
$post = $_SESSION;

require_once 'config.php';
require 'config.php';
include 'config.php';
include_once 'config.php';

function myFunc(AdapterInterface $adapter)
{
    $eol = PHP_EOL;
}

$str = 'foo';
eval ("\$str = \"$str\"; new DateTime;");

$str = trim($str);

call_user_func($str);
call_user_func_array($str, array());
forward_static_call($str);
forward_static_call_array($str);
create_function($str, $str);

$var = 'baz';
$$var;
$staticClass::$staticMethod();
$func();

new $dynClassBrackets();
new $dynClass;

// shell exec
// global in func, static in func
