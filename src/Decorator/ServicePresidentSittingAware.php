<?php

namespace App\Decorator;

use App\Service\PresidentSitting;

interface ServicePresidentSittingAware
{
    public function setPresidentSittingService(PresidentSitting $presidentSitting): self;
}
