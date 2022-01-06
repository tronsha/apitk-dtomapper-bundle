<?php

declare(strict_types=1);

namespace Shopping\ApiTKDtoMapperBundle\DtoMapper;

/**
 * Interface MapperCollectionInterface.
 *
 * @package Shopping\ApiTKDtoMapperBundle\DtoMapper
 */
interface MapperCollectionInterface
{
    /**
     * @param mixed[] $items
     *
     * @return mixed
     */
    public function mapCollection(array $items);
}
