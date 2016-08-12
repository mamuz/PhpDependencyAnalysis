<?php

namespace PackageA\Controller;

use PackageA\Service\Query as QueryService;

class Query extends AbstractController
{
    /** @var QueryService */
    private $service;

    public function __construct(QueryService $service)
    {
        $this->service = $service;
    }

    public function readAction()
    {
        $request = $this->getRequest();
        $data = $request->getQueryData();

        $this->service->find($data);
    }
}
