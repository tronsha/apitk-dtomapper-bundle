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
 * Class ProtobufViewHandler.
 *
 * ViewHandler for protobuf responses.
 *
 * @package Shopping\ApiTKDtoMapperBundle\Handler
 */
class ProtobufViewHandler
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

        if (!$data instanceof Message) {
            return new Response(
                '',
                Response::HTTP_NOT_ACCEPTABLE,
                $view->getHeaders()
            );
        }

        return new Response(
            $data->serializeToString(),
            $view->getStatusCode() ?? 200,
            array_merge($view->getHeaders(), ['content-type' => 'application/x-protobuf'])
        );
    }
}
