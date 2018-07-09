<?php /** @noinspection PhpUnusedParameterInspection */

namespace Ofeige\Rfc1Bundle\Handler;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PhpViewHandler
 *
 * ViewHandler for the fast serialize() responses. (Map "dto" type to the wished mime type in your fos_rest.yaml)
 *
 * @package Ofeige\Rfc1Bundle\Handler
 */
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
        return new Response(serialize($view->getData()), $view->getStatusCode() ?? 200);
    }
}