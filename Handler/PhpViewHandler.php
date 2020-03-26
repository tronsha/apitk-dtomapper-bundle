<?php

/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace Shopping\ApiTKDtoMapperBundle\Handler;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use Symfony\Component\Debug\Exception\FlattenException as LegacyFlattenException;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

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

        // Use simplified exception because serialization of closures inside the real exception is not allowed and crashes
        if ($data instanceof Throwable) {
            // symfony/debug component was deprecated in symfony 4.4 and replaced by symfony/error-handler
            // both of them are part of the standard framework-bundle which is required by this project so
            // we can safely assume that one of them is present.
            // to allow symfony 4.3 compatibility, use the FlattenException from symfony/debug when symfony/error-handler
            // is not available
            $flattenExceptionClass = !class_exists(FlattenException::class) ? FlattenException::class : LegacyFlattenException::class;
            $data = $flattenExceptionClass::createFromThrowable($data, $view->getStatusCode(), $view->getHeaders());
        }

        return new Response(serialize($data), $view->getStatusCode() ?? 200, $view->getHeaders());
    }
}
