<?php

namespace PhpDaTest\Stub;

use PhpDA\Entity\AnalysisAwareInterface as B;
use PhpDA\Entity\AnalysisAwareTrait;
use PhpDA\Entity\AnalysisCollection;
use PhpDA\Service\AnalyzerFactory;
use PhpDA\Service\WriteAdapterFactory;
use PhpDA\Writer\AdapterInterface;

class MyClassCycle
{
    use AnalysisAwareTrait;

    public function __construct()
    {
        $test = new MyClass();
    }
}

class MyClass extends \SplObjectStorage implements B
{
    use AnalysisAwareTrait;

    public function __construct()
    {
        $test = new MyClassCycle();
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

$var = true;
$var = null;
$var = false;
$var = null;

require_once 'config.php';
require 'config.php';
include 'config.php';
include_once 'config.php';

function myFunc(AdapterInterface $adapter)
{
    global $post;
    $eol = PHP_EOL;
}

$str = 'foo';
eval ("\$str = \"$str\"; new DateTime;");

$str = trim($str);

call_user_func($str);
call_user_func_array($str, array());
forward_static_call($str);
forward_static_call_array($str);
call_user_method($str, $str);
call_user_method_array($str, $str, array());
create_function($str, $str);

$staticClass = $staticMethod = $var = $func = $dynClassBrackets = $dynClass = 'baz';
$$var;
$staticClass::$staticMethod();
$func();

new $dynClassBrackets();
new $dynClass;

exec('foo');
passthru('foo');
proc_open('foo', array(), $var);
shell_exec('foo');
system('foo');
`test`;

$nameSpace1 = '\Test\Object';
$nameSpace2 = '\Test';
$nameSpace3 = 'Test_Object';

$class = new \stdClass();

$class->$test();
$class->get();
$class->get('test');
