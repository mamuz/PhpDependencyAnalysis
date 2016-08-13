<?php

namespace PackageA\Service;

use PackageB\Service\Message as PackageService;

class Message
{
    public function __construct()
    {
        new PackageService;
    }
}
