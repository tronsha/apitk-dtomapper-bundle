<?php

namespace Ofeige\Rfc1Bundle\EventListener;

use Ofeige\Rfc1Bundle\DtoMapper\MapperInterface;
use Ofeige\Rfc1Bundle\Exception\MapperException;
use Ofeige\Rfc1Bundle\Service\ArrayHelper;
use Doctrine\Common\Annotations\Reader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

use Ofeige\Rfc1Bundle\Annotation as Rfc1;

/**
 * Class MapperListener
 *
 * Applies the Rfc1\View dtoMapper to the by the controller action returned data, so a DTO (or array of DTOs) goes to
 * the response.
 *
 * @package Ofeige\Rfc1Bundle\EventListener
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
     * @param Reader $reader
     * @param ArrayHelper $arrayHelper
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
     * @param callable $controller
     * @return null|Rfc1\View
     */
    private function getViewAnnotationByController(callable $controller): ?Rfc1\View
    {
        /** @var Controller $controllerObject */
        list($controllerObject, $methodName) = $controller;

        $controllerReflectionObject = new \ReflectionObject($controllerObject);
        $reflectionMethod = $controllerReflectionObject->getMethod($methodName);

        $annotations = $this->reader->getMethodAnnotations($reflectionMethod);
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Rfc1\View) {
                return $annotation;
            }
        }

        return null;
    }

    /**
     * @param object|array $data
     * @param MapperInterface $mapper
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