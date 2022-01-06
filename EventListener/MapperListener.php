<?php

declare(strict_types=1);

namespace Shopping\ApiTKDtoMapperBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Exception;
use ReflectionException;
use ReflectionObject;
use Shopping\ApiTKDtoMapperBundle\Annotation as Dto;
use Shopping\ApiTKDtoMapperBundle\DtoMapper\MapperCollectionInterface;
use Shopping\ApiTKDtoMapperBundle\DtoMapper\MapperInterface;
use Shopping\ApiTKDtoMapperBundle\Exception\MapperException;
use Shopping\ApiTKDtoMapperBundle\Exception\UnmappableException;
use Shopping\ApiTKDtoMapperBundle\Service\ArrayHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;

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
     * @param ViewEvent $event
     *
     * @throws MapperException
     * @throws ReflectionException
     */
    public function onKernelView(ViewEvent $event): void
    {
        // only transform on original action
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
            $mapper = $this->container->get((string) $view->getDtoMapper());
        } catch (Exception $e) {
            throw new MapperException(sprintf('Mapper "%s" could not be used. %s', $view->getDtoMapper(), $e->getMessage()), 500, $e);
        }

        $data = $event->getControllerResult();

        $event->setControllerResult($this->mapData($data, $mapper));
    }

    /**
     * @param mixed $controller
     *
     * @throws ReflectionException
     *
     * @return Dto\View|null
     * @return Dto\View|null
     */
    private function getViewAnnotationByController($controller): ?Dto\View
    {
        if (is_array($controller)) {
            list($controllerObject, $methodName) = $controller;
        } else {
            $controllerObject = $controller;
            $methodName = '__invoke';
        }

        /** @var AbstractController $controllerObject */
        $controllerReflectionObject = new ReflectionObject($controllerObject);
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
     * @param mixed           $data
     * @param MapperInterface $mapper
     *
     * @return mixed
     */
    private function mapData($data, MapperInterface $mapper)
    {
        if (is_array($data) && $this->arrayHelper->isNumeric($data)) {
            $mappedData = [];
            foreach ($data as $entry) {
                try {
                    $mappedData[] = $mapper->map($entry);
                } catch (UnmappableException $exception) {
                    // Data is not mappable and therefore we don't add it to the array
                }
            }
            if ($mapper instanceof MapperCollectionInterface) {
                $mappedData = $mapper->mapCollection($mappedData);
            }
        } else {
            try {
                $mappedData = $mapper->map($data);
            } catch (UnmappableException $exception) {
                $mappedData = null;
            }
        }

        return $mappedData;
    }
}
