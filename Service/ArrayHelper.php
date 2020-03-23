<?php

declare(strict_types=1);

namespace Shopping\ApiTKDtoMapperBundle\Service;

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
     * @param array $array
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function isNumeric(array $array): bool
    {
        if (empty($array)) {
            return true;
        }

        foreach ($array as $key => $value) {
            if (!is_int($key)) {
                return false;
            }
        }

        return true;
    }
}
