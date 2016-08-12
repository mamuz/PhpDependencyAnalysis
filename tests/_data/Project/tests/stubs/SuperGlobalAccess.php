<?php

namespace PackageX;

class SuperGlobalAccess
{
    public function __construct()
    {
        $post = $_POST;
        $get = $_GET;
        $req = $_REQUEST;
        $cookie = $_COOKIE;
        $files = $_FILES;
        $server = $_SERVER;
        $env = $_ENV;
        $globals = $GLOBALS;
        $session = $_SESSION;
    }
}
