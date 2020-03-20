<?php

declare(strict_types=1);

namespace Shopping\ApiTKDtoMapperBundle\Service;

use Traversable;

/**
 * Class ArrayHelper.
 *
 * @package App\Service
 */
class ArrayHelper
{
    /**
     * Returns if the array has only numeric keys.
     *
     * @param iterable $array
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function isNumeric(iterable $array): bool
    {
        if (!$array instanceof Traversable) {
            return false;
        }
        foreach ($array as $key => $value) {
            if (!is_int($key)) {
                return false;
            }
        }

        return true;
    }
}
