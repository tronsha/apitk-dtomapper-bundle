<?php

/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace Shopping\ApiTKDtoMapperBundle\Handler;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PhpViewHandler.
 *
 * ViewHandler for the fast serialize() responses. (Map "dto" type to the wished mime type in your fos_rest.yaml)
 *
 * @package Shopping\ApiTKDtoMapperBundle\Handler
 */
class PhpViewHandler
{
    /**
     * @param ViewHandler $handler
     * @param View        $view
     * @param Request     $request
     * @param string|null $format
     *
     * @return Response
     */
    public function createResponse(ViewHandler $handler, View $view, Request $request, $format): Response
    {
        return new Response(serialize($view->getData()), $view->getStatusCode() ?? 200, $view->getHeaders());
    }
}
