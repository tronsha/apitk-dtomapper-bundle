<?php
declare(strict_types=1);

namespace Shopping\ApiDtoMapperBundle\Service;

/**
 * Class StringHelper
 * @package Shopping\ApiDtoMapperBundle\Service
 */
class StringHelper
{
    /**
     * Returns the unqualified (short) class name of a fully qualified class name.
     *
     * @param string $type
     * @return null|string
     */
    public function getShortTypeByType(string $type): ?string
    {
        try {
            return (new \ReflectionClass($type))->getShortName();
        } catch (\ReflectionException $e) {
            return null;
        }
    }

    /**
     * Adds an "a" or "an" to a word.
     *
     * @param string $string
     * @return string
     */
    public function addA(string $string): string
    {
        if (in_array($string[0], ['a','e','i','o','u'])) {
            return 'an ' . $string;
        }

        return 'a ' . $string;
    }
}