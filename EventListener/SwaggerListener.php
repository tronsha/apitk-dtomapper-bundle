<?php

namespace Ofeige\Rfc1Bundle\EventListener;

use Nelmio\ApiDocBundle\Controller\SwaggerUiController;
use Swagger\Annotations\Get;
use Swagger\Annotations\Operation;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class ControllerListener
 * @package Ofeige\Rfc1Bundle\EventListener
 *
 * Because we auto generate responses, we have to tell Swagger that no response annotation is required. (Kinda hacky :/)
 */
class SwaggerListener
{
    /**
     * @var bool
     */
    private $masterRequest = true;

    /**
     * @var callable|null
     */
    private $calledController = null;

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

    public function getCalledController(): ?callable
    {
        return $this->calledController;
    }
}