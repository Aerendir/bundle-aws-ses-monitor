<?php
namespace Shivas\BouncerBundle\Model;

use Symfony\Component\HttpFoundation\Request;

class NoopHandler implements BouncerHandlerInterface
{
    /**
     * @param Request $request
     * @return int
     */
    public function handleRequest(Request $request)
    {
        return 200;
    }
}
