<?php

namespace Shopping\ApiDtoMapperBundle;

use Shopping\ApiDtoMapperBundle\DependencyInjection\ShoppingApiDtoMapperExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ShoppingApiDtoMapperBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new ShoppingApiDtoMapperExtension();
    }
}