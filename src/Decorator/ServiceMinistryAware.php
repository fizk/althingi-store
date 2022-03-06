<?php

namespace App\Decorator;

use App\Service\Ministry;

interface ServiceMinistryAware
{
    public function setMinistryService(Ministry $ministry): self;
}
