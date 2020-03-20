<?php

declare(strict_types=1);

namespace Shopping\ApiTKDtoMapperBundle\DtoMapper;

/**
 * Interface MapperInterface.
 *
 * @package Shopping\ApiTKDtoMapperBundle\DtoMapper
 */
interface MapperInterface
{
    /**
     * Maps the incoming object into one DTO object.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function map($data);
}
