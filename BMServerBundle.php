<?php

declare(strict_types=1);

namespace BMServerBundle\Server;

use BMServerBundle\Server\DependencyInjection\BMServerExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BMServerBundle extends Bundle
{
    public const VERSION = '0.0.1-beta1';

    public function getContainerExtension()
    {
        return new BMServerExtension();
    }
}
