<?php

namespace Mocchi\Http;

use App\Container;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Controller extends AbstractController
{
    protected Container $storage;

    protected function getContainer(): Container
    {

        if(!isset($this->storage)) {

            $this->storage = new Container();
        }

        return $this->storage;
    }
}