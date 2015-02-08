<?php
namespace Shivas\BouncerBundle\Model;

use Symfony\Component\HttpFoundation\Request;

interface BouncerHandlerInterface
{
    /**
     * @param Request $request
     * @return int
     */
    public function handleRequest(Request $request);
}
