<?php

namespace Shopping\ApiDtoMapperBundle\Describer;


use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;
use EXSyst\Component\Swagger\Operation;
use EXSyst\Component\Swagger\Response;
use EXSyst\Component\Swagger\Swagger;
use Nelmio\ApiDocBundle\Describer\DescriberInterface;
use Nelmio\ApiDocBundle\Describer\ModelRegistryAwareInterface;
use Nelmio\ApiDocBundle\Describer\ModelRegistryAwareTrait;
use Nelmio\ApiDocBundle\Model\Model;
use Nelmio\ApiDocBundle\Util\ControllerReflector;
use Shopping\ApiDtoMapperBundle\Service\StringHelper;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Routing\RouteCollection;
use Shopping\ApiDtoMapperBundle\Annotation AS Rfc1;

/**
 * Class AnnotationDescriber
 *
 * Auto generate 200-responses by the annotated dtoMapper.
 *
 * The following conditions must match:
 * * No Response(200) annotation given
 * * Rfc1\View annotation with dtoMapper given
 * * Corresponding dtoMapper has a return typehint
 * * Controller action has a return-annotation, which states the return of an array or not (f.e. * @ return Foobar[])
 *
 * @package Shopping\ApiDtoMapperBundle\Describer
 */
class AnnotationDescriber implements DescriberInterface, ModelRegistryAwareInterface
{
    use ModelRegistryAwareTrait;

    /**
     * @var RouteCollection
     */
    private $routeCollection;
    /**
     * @var ControllerReflector
     */
    private $controllerReflector;
    /**
     * @var Reader
     */
    private $reader;
    /**
     * @var StringHelper
     */
    private $stringHelper;

    //TODO: Replace ControllerReflector with something we can depend on
    /**
     * @param RouteCollection $routeCollection
     * @param ControllerReflector $controllerReflector
     * @param Reader $reader
     * @param StringHelper $stringHelper
     */
    public function __construct(
        RouteCollection $routeCollection,
        ControllerReflector $controllerReflector,
        Reader $reader,
        StringHelper $stringHelper
    ) {
        $this->routeCollection = $routeCollection;
        $this->controllerReflector = $controllerReflector;
        $this->reader = $reader;
        $this->stringHelper = $stringHelper;
    }

    /**
     * @param Swagger $api
     */
    public function describe(Swagger $api)
    {
        $paths = $api->getPaths();
        foreach ($paths as $uri => $path) {
            foreach ($path->getMethods() as $method) {
                /** @var Operation $operation */
                $operation = $path->getOperation($method);

                foreach ($this->getMethodsToParse() as $classMethod => list($methodPath, $httpMethods)) {
                    if ($methodPath === $uri && in_array($method, $httpMethods)) {
                        $responseExisting = $operation->getResponses()->has(200);
                        if ($responseExisting) {
                            continue;
                        }

                        $view = $this->getView($this->reader->getMethodAnnotations($classMethod));
                        if (!$view) {
                            continue;
                        }

                        $type = $this->getDtoByMapper($view->getDtoMapper());
                        if (!$type) {
                            continue;
                        }

                        $shortType = $this->stringHelper->getShortTypeByType($type);
                        if (!$shortType) {
                            continue;
                        }

                        $isArray = $this->willReturnArray($classMethod);

                        $reference = $this->modelRegistry->register(new Model(new Type(Type::BUILTIN_TYPE_OBJECT, false, $type)));

                        $operation->getResponses()->set(200, $this->getResponse($isArray, $shortType, $reference));
                    }
                }
            }
        }
    }

    /**
     * @return \Generator
     */
    private function getMethodsToParse(): \Generator
    {
        foreach ($this->routeCollection->all() as $route) {
            if (!$route->hasDefault('_controller')) {
                continue;
            }

            $controller = $route->getDefault('_controller');
            if ($callable = $this->controllerReflector->getReflectionClassAndMethod($controller)) {
                $path = $this->normalizePath($route->getPath());
                $httpMethods = $route->getMethods() ?: Swagger::$METHODS;
                $httpMethods = array_map('strtolower', $httpMethods);
                $supportedHttpMethods = array_intersect($httpMethods, Swagger::$METHODS);

                if (empty($supportedHttpMethods)) {
                    continue;
                }

                yield $callable[1] => [$path, $supportedHttpMethods];
            }
        }
    }

    /**
     * @param string $path
     * @return string
     */
    private function normalizePath(string $path): string
    {
        if ('.{_format}' === substr($path, -10)) {
            $path = substr($path, 0, -10);
        }

        return $path;
    }

    /**
     * Returns the view annotation.
     *
     * @param Annotation[] $annotations
     * @return null|Rfc1\View
     */
    private function getView(array $annotations): ?Rfc1\View
    {
        $views = array_filter($annotations, function($annotation) { return $annotation instanceof Rfc1\View; });
        if (!count($views)) {
            return null;
        }

        return reset($views);
    }

    /**
     * Returns the Dto class name the Mapper will return.
     *
     * @param string $mapper
     * @return null|string
     */
    private function getDtoByMapper(string $mapper): ?string
    {
        try {
            $viewReflectionMethod = new \ReflectionMethod($mapper, 'map');

            return $viewReflectionMethod->getReturnType()->getName();
        } catch (\ReflectionException $e) {
            return null;
        }
    }

    /**
     * Returns the response object for swagger.
     *
     * @param bool $isArray
     * @param string $shortType
     * @param string $reference
     * @return Response
     */
    private function getResponse(bool $isArray, string $shortType, string $reference): Response
    {
        $typeDefinition = [
            '$ref' => $reference,
        ];

        if ($isArray) {
            $response = new Response([
                'description' => 'Will return an array of ' . $shortType . ' DTOs on success',
                'schema' => [
                    'type' => 'array',
                    'items' => $typeDefinition,
                ],
            ]);
        } else {
            $response = new Response([
                'description' => 'Will return ' . $this->stringHelper->addA($shortType) . ' DTO on success',
                'schema' => $typeDefinition,
            ]);
        }

        return $response;
    }

    /**
     * Returns true, if the methods returns an array (by the return annotation).
     *
     * @param \ReflectionMethod $method
     * @return bool
     */
    private function willReturnArray(\ReflectionMethod $method): bool
    {
        return (bool) preg_match("/@return[ \t]+([^ \t\n\r\\[\\]]+\\[\\])/", $method->getDocComment());
    }
}