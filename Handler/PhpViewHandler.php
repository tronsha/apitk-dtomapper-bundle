<?php /** @noinspection PhpUnusedParameterInspection */

namespace Shopping\ApiTKDtoMapperBundle\Handler;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PhpViewHandler
 *
 * ViewHandler for the fast serialize() responses. (Map "dto" type to the wished mime type in your fos_rest.yaml)
 *
 * @package Shopping\ApiTKDtoMapperBundle\Handler
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
        $data = $view->getData();

        // Use simplified exception because serialization of closures inside the real exception is not allowed and crashes
        if ($view->getTemplate() === 'raw_exception') {
            $data = $view->getTemplateData()['exception'];
        }

        return new Response(serialize($data), $view->getStatusCode() ?? 200,  $view->getHeaders());
    }
}
