<?php

namespace App\Decorator;

use App\Service\MinisterSitting;

interface ServiceMinisterSittingAware
{
    public function setMinisterSittingService(MinisterSitting $ministerSitting): self;
}
