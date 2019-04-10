<?php

namespace PackageA\Controller;

use Zend\Http\Request as HttpRequest;
use Zend\Mvc\Controller\AbstractController as AbstractZendController;

abstract class AbstractController extends AbstractZendController
{
    /**
     * @return HttpRequest
     */
    protected function getRequest()
    {
        return new HttpRequest();
    }
}
