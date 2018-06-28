<?php

namespace Ofeige\Rfc1Bundle\Handler;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PhpViewHandler
{
    /**
     * @param ViewHandler $handler
     * @param View $view
     * @param Request $request
     * @param $format
     * @return Response
     */
    public function createResponse(ViewHandler $handler, View $view, Request $request, $format)
    {
        return new Response('foobar', 200);
    }
}