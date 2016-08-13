<?php

namespace PackageB\Service;

use PackageC\Service\Message as PackageService;

class Message
{
    public function __construct()
    {
        new PackageService;
    }
}
