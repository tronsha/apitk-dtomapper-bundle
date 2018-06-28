<?php

namespace Ofeige\Rfc1Bundle\EventListener;

use Ofeige\Rfc1Bundle\Exception\MapperException;
use Ofeige\Rfc1Bundle\Service\ArrayHelper;
use Doctrine\Common\Annotations\Reader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

use Ofeige\Rfc1Bundle\Annotation as Rfc1;

class MapperListener
{
    /**
     * @var bool
     */
    private $masterRequest = true;
    /**
     * @var ControllerListener
     */
    private $controllerListener;
    /**
     * @var Reader
     */
    private $reader;
    /**
     * @var ArrayHelper
     */
    private $arrayHelper;
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ControllerListener $controllerListener, Reader $reader, ArrayHelper $arrayHelper, ContainerInterface $container)
    {
        $this->controllerListener = $controllerListener;
        $this->reader = $reader;
        $this->arrayHelper = $arrayHelper;
        $this->container = $container;
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     * @throws MapperException
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        //Only transform on original action
        if (!$this->masterRequest) {
            return;
        }
        $this->masterRequest = false;

        if (!$this->controllerListener->getCalledController()) {
            return;
        }

        $methodAnnotations = $this->getAnnotationsByController($this->controllerListener->getCalledController());
        foreach ($methodAnnotations as $annotation) {
            if (!$annotation instanceof Rfc1\View) {
                continue;
            }

            try {
                $mapper = $this->container->get($annotation->getDtoMapper());
            } catch (\Exception $e) {
                throw new MapperException(sprintf('Mapper "%s" could not be used. %s', $annotation->getDtoMapper(), $e->getMessage()), 500, $e);
            }

            $data = $event->getControllerResult();
            if ($this->arrayHelper->isNumeric($data)) {
                $mappedData = [];
                foreach ($data as $entry) {
                    $mappedData[] = $mapper->map($entry);
                }
            } else {
                $mappedData = $mapper->map($data);
            }

            $event->setControllerResult($mappedData);
            break;
        }
    }

    private function getAnnotationsByController(callable $controller): array
    {
        /** @var Controller $controllerObject */
        list($controllerObject, $methodName) = $controller;

        $controllerReflectionObject = new \ReflectionObject($controllerObject);
        $reflectionMethod = $controllerReflectionObject->getMethod($methodName);

        return $this->reader->getMethodAnnotations($reflectionMethod);
    }
}