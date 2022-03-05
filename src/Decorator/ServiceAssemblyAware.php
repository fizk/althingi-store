<?php

namespace App\Decorator;

use App\Service\Assembly;

interface ServiceAssemblyAware
{
    public function setAssemblyService(Assembly $assembly): self;
}