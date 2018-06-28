<?php

namespace Ofeige\Rfc1Bundle;

use Ofeige\Rfc1Bundle\DependencyInjection\OfeigeRfc1Extension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OfeigeRfc1Bundle extends Bundle
{
    public function getContainerExtension()
    {
        return new OfeigeRfc1Extension();
    }
}