<?php

namespace Shopping\ApiTKDtoMapperBundle\Annotation;

use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * Class View.
 *
 * @package App\Annotation
 *
 * @Annotation
 */
class View extends Rest\View
{
    /**
     * @var string|null
     */
    private $dtoMapper;

    /**
     * @return string|null
     */
    public function getDtoMapper(): ?string
    {
        return $this->dtoMapper;
    }

    /**
     * @param string|null $dtoMapper
     *
     * @return View
     */
    public function setDtoMapper(?string $dtoMapper): View
    {
        $this->dtoMapper = $dtoMapper;

        return $this;
    }
}
