<?php

namespace Shopping\ApiDtoMapperBundle\DtoMapper;

/**
 * Interface MapperInterface
 * @package Shopping\ApiDtoMapperBundle\DtoMapper
 */
interface MapperInterface
{
    /**
     * Maps the incoming object into one DTO object.
     *
     * @param $data
     * @return mixed
     */
    public function map($data);
}