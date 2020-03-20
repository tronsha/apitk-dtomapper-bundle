<?php

namespace Shopping\ApiTKDtoMapperBundle\Describer;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;
use EXSyst\Component\Swagger\Operation;
use EXSyst\Component\Swagger\Path;
use EXSyst\Component\Swagger\Response;
use Nelmio\ApiDocBundle\Describer\ModelRegistryAwareInterface;
use Nelmio\ApiDocBundle\Describer\ModelRegistryAwareTrait;
use Nelmio\ApiDocBundle\Model\Model;
use Shopping\ApiTKCommonBundle\Describer\AbstractDescriber;
use Shopping\ApiTKDtoMapperBundle\Annotation as DtoMapper;
use Shopping\ApiTKDtoMapperBundle\Service\StringHelper;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class AnnotationDescriber.
 *
 * Auto generate 200-responses by the annotated dtoMapper.
 *
 * The following conditions must match:
 * * No Response(200) annotation given
 * * Dto\View annotation with dtoMapper given
 * * Corresponding dtoMapper has a return typehint
 *
 * * Controller action has a return-annotation, which states the return of an array or not (f.e. * @ return Foobar[])
 *
 * @package Shopping\ApiTKDtoMapperBundle\Describer
 */
class AnnotationDescriber extends AbstractDescriber implements ModelRegistryAwareInterface
{
    use ModelRegistryAwareTrait;

    /**
     * @var StringHelper
     */
    private $stringHelper;

    /**
     * AnnotationDescriber constructor.
     *
     * @param RouteCollection                                      $routeCollection
     * @param \Shopping\ApiTKCommonBundle\Util\ControllerReflector $controllerReflector
     * @param Reader                                               $reader
     * @param StringHelper                                         $stringHelper
     */
    public function __construct(
        RouteCollection $routeCollection,
        \Shopping\ApiTKCommonBundle\Util\ControllerReflector $controllerReflector,
        Reader $reader,
        StringHelper $stringHelper
    ) {
        parent::__construct($routeCollection, $controllerReflector, $reader);
        $this->stringHelper = $stringHelper;
    }

    /**
     * @param Operation         $operation
     * @param \ReflectionMethod $classMethod
     * @param Path              $path
     * @param string            $method
     */
    protected function handleOperation(
        Operation $operation,
        \ReflectionMethod $classMethod,
        Path $path,
        string $method
    ): void {
        $responseExisting = $operation->getResponses()->has(200);
        if ($responseExisting) {
            return;
        }

        $view = $this->getView($this->reader->getMethodAnnotations($classMethod));
        if (!$view) {
            return;
        }

        $type = $this->getDtoByMapper($view->getDtoMapper());
        if (!$type) {
            return;
        }

        $shortType = $this->stringHelper->getShortTypeByType($type);
        if (!$shortType) {
            return;
        }

        $isArray = $this->willReturnArray($classMethod);

        $reference = $this->modelRegistry->register(new Model(new Type(Type::BUILTIN_TYPE_OBJECT, false, $type)));

        $operation->getResponses()->set(200, $this->getResponse($isArray, $shortType, $reference));
    }

    /**
     * Returns the view annotation.
     *
     * @param Annotation[] $annotations
     *
     * @return DtoMapper\View|null
     */
    private function getView(array $annotations): ?DtoMapper\View
    {
        $views = array_filter($annotations, function ($annotation) {
            return $annotation instanceof DtoMapper\View;
        });
        if (!count($views)) {
            return null;
        }

        return reset($views);
    }

    /**
     * Returns the Dto class name the Mapper will return.
     *
     * @param string $mapper
     *
     * @return string|null
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
     * @param bool   $isArray
     * @param string $shortType
     * @param string $reference
     *
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
     *
     * @return bool
     */
    private function willReturnArray(\ReflectionMethod $method): bool
    {
        return (bool) preg_match("/@return[ \t]+([^ \t\n\r\\[\\]]+\\[\\])/", $method->getDocComment());
    }
}
