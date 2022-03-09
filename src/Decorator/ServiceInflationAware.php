<?php

namespace App\Decorator;

use App\Service\Inflation;

interface ServiceInflationAware
{
    public function setInflationService(Inflation $inflation): self;
}
