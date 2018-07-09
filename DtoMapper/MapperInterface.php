<?php

namespace Ofeige\Rfc1Bundle\DtoMapper;

/**
 * Interface MapperInterface
 * @package Ofeige\Rfc1Bundle\DtoMapper
 */
interface MapperInterface
{
    /**
     * Mappes the incoming object into one DTO object.
     *
     * @param $data
     * @return mixed
     */
    public function map($data);
}