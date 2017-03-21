<?php

namespace PackageA\Controller;

use PackageA\Service\Command as CommandService;

class Command extends AbstractController
{
    /** @var CommandService */
    private $service;

    public function __construct(CommandService $service)
    {
        $this->service = $service;
    }

    public function createAction()
    {
        $request = $this->getRequest();
        $data = $request->getPostData();

        $this->service->persist($data);
    }

    public function updateAction()
    {
        $request = $this->getRequest();
        $data = $request->getPostData();

        $this->service->persist($data);
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        $data = $request->getPostData();

        $this->service->delete($data['id']);
    }
}
