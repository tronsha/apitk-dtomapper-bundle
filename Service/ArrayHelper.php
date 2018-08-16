<?php

namespace Shopping\ApiTKDtoMapperBundle\Service;

/**
 * Class ArrayHelper
 * @package App\Service
 */
class ArrayHelper
{
    /**
     * Returns if the array has only numeric keys.
     *
     * @param array $array
     * @return bool
     */
    public function isNumeric($array): bool
    {
        if (!is_array($array) && !$array instanceof \Traversable) return false;

        foreach ($array as $key => $value) {
            if (!is_int($key)) {
                return false;
            }
        }

        return true;
    }
}