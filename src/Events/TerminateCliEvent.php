<?php

namespace Symfony\Component\HttpKernel\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class TerminateCliEvent extends KernelEvent
{
    public function __construct(HttpKernelInterface $kernel)
    {
        parent::__construct($kernel, new Request(), HttpKernelInterface::MASTER_REQUEST);
    }

    public function getResponse(): void
    {

    }
}