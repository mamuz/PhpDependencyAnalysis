<?php

namespace PackageC\Service;

use PackageA\Service\Message as PackageService;

class Message
{
    public function __construct()
    {
        new PackageService;
    }
}
