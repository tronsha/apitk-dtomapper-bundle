<?php

declare(strict_types=1);

namespace Shopping\ApiTKDtoMapperBundle\EventListener;

use Nelmio\ApiDocBundle\Controller\SwaggerUiController;
use ReflectionClass;
use ReflectionException;
use Swagger\Annotations\Get;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

/**
 * Class ControllerListener.
 *
 * @package Shopping\ApiTKDtoMapperBundle\EventListener
 *
 * Because we auto generate responses, we have to tell Swagger that no response annotation is required. (Kinda hacky :/)
 */
class SwaggerListener
{
    /**
     * @param ControllerEvent $event
     */
    public function onKernelController(ControllerEvent $event): void
    {
        if ($event->getController() instanceof SwaggerUiController) {
            try {
                $reflectionClass = new ReflectionClass(Get::class);

                /** @phpstan-ignore-next-line */
                $reflectionClass->setStaticPropertyValue('_required', []);
            } catch (ReflectionException $e) {
                // suppress
            }
        }
    }
}
