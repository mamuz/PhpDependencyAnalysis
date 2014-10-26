<?php

namespace PackageB\Controller;

use PackageB\Entity\Package;
use PackageB\Mapper\CommandInterface;

class Command extends AbstractController
{
    /** @var CommandInterface */
    private $mapper;

    public function __construct(CommandInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    public function createAction()
    {
        $request = $this->getRequest();
        $data = $request->getPostData();

        $this->mapper->persist(new Package($data));
    }

    public function updateAction()
    {
        $request = $this->getRequest();
        $data = $request->getPostData();

        $this->mapper->persist(new Package($data));
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        $data = $request->getPostData();

        $this->mapper->delete($data['id']);
    }
}
