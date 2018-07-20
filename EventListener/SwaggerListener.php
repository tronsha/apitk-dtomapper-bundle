<?php

namespace Shopping\ApiDtoMapperBundle\EventListener;

use Nelmio\ApiDocBundle\Controller\SwaggerUiController;
use Swagger\Annotations\Get;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class ControllerListener
 * @package Shopping\ApiDtoMapperBundle\EventListener
 *
 * Because we auto generate responses, we have to tell Swagger that no response annotation is required. (Kinda hacky :/)
 */
class SwaggerListener
{
    public function onKernelController(FilterControllerEvent $event)
    {
        if ($event->getController() instanceof SwaggerUiController) {
            try {
                $reflectionClass = new \ReflectionClass(Get::class);
                $reflectionClass->setStaticPropertyValue('_required', []);
            } catch (\ReflectionException $e) {
            }
        }
    }
}