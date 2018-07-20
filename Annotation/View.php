<?php

namespace Shopping\ApiDtoMapperBundle\Annotation;

use \FOS\RestBundle\Controller\Annotations as Rest;

/**
 * Class View
 * @package App\Annotation
 *
 * @Annotation
 */
class View extends Rest\View
{
    /**
     * @var string|null
     */
    private $dtoMapper = null;

    /**
     * @return null|string
     */
    public function getDtoMapper(): ?string
    {
        return $this->dtoMapper;
    }

    /**
     * @param null|string $dtoMapper
     * @return View
     */
    public function setDtoMapper(?string $dtoMapper): View
    {
        $this->dtoMapper = $dtoMapper;
        return $this;
    }
}