<?php

/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace Shopping\ApiTKDtoMapperBundle\Handler;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use Google\Protobuf\Internal\Message;
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
        $data = $view->getData();

        if ($data instanceof Message) {
            return new Response(
                '',
                Response::HTTP_NOT_ACCEPTABLE,
                $view->getHeaders()
            );
        }

        return new Response(serialize($data), $view->getStatusCode() ?? 200, $view->getHeaders());
    }
}
