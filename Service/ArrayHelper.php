<?php

namespace Ofeige\Rfc1Bundle\Service;

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
    public function isNumeric(array $array): bool
    {
        foreach ($array as $key => $value) {
            if (!is_int($key)) {
                return false;
            }
        }

        return true;
    }
}