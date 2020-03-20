<?php

namespace Shopping\ApiTKDtoMapperBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Shopping\ApiTKDtoMapperBundle\Annotation as Dto;
use Shopping\ApiTKDtoMapperBundle\DtoMapper\MapperInterface;
use Shopping\ApiTKDtoMapperBundle\Exception\MapperException;
use Shopping\ApiTKDtoMapperBundle\Service\ArrayHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Class MapperListener.
 *
 * Applies the Dto\View dtoMapper to the by the controller action returned data, so a DTO (or array of DTOs) goes to
 * the response.
 *
 * @package Shopping\ApiTKDtoMapperBundle\EventListener
 */
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

    /**
     * @param ControllerListener $controllerListener
     * @param Reader             $reader
     * @param ArrayHelper        $arrayHelper
     * @param ContainerInterface $container
     */
    public function __construct(ControllerListener $controllerListener, Reader $reader, ArrayHelper $arrayHelper, ContainerInterface $container)
    {
        $this->controllerListener = $controllerListener;
        $this->reader = $reader;
        $this->arrayHelper = $arrayHelper;
        $this->container = $container;
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     *
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

        $view = $this->getViewAnnotationByController($this->controllerListener->getCalledController());
        if (!$view) {
            return;
        }

        try {
            /** @var MapperInterface $mapper */
            $mapper = $this->container->get($view->getDtoMapper());
        } catch (\Exception $e) {
            throw new MapperException(sprintf('Mapper "%s" could not be used. %s', $view->getDtoMapper(), $e->getMessage()), 500, $e);
        }

        $data = $event->getControllerResult();

        $event->setControllerResult($this->mapData($data, $mapper));
    }

    /**
     * @param mixed $controller
     *
     * @throws \ReflectionException
     *
     * @return Dto\View|null
     */
    private function getViewAnnotationByController($controller): ?Dto\View
    {
        /** @var Controller $controllerObject */
        if (is_array($controller)) {
            list($controllerObject, $methodName) = $controller;
        } else {
            $controllerObject = $controller;
            $methodName = '__invoke';
        }

        $controllerReflectionObject = new \ReflectionObject($controllerObject);
        $reflectionMethod = $controllerReflectionObject->getMethod($methodName);

        $annotations = $this->reader->getMethodAnnotations($reflectionMethod);
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Dto\View) {
                return $annotation;
            }
        }

        return null;
    }

    /**
     * @param object|array    $data
     * @param MapperInterface $mapper
     *
     * @return object|array
     */
    private function mapData($data, MapperInterface $mapper)
    {
        if ($this->arrayHelper->isNumeric($data)) {
            $mappedData = [];
            foreach ($data as $entry) {
                $mappedData[] = $mapper->map($entry);
            }
        } else {
            $mappedData = $mapper->map($data);
        }

        return $mappedData;
    }
}
