<?php

namespace Shopping\ApiTKDtoMapperBundle;

use Shopping\ApiTKDtoMapperBundle\DependencyInjection\ShoppingApiTKDtoMapperExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ShoppingApiTKDtoMapperBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new ShoppingApiTKDtoMapperExtension();
    }
}
