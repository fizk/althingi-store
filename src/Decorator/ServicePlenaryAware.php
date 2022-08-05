<?php

namespace App\Decorator;

use App\Service\Plenary;

interface ServicePlenaryAware
{
    public function setPlenaryService(Plenary $plenary): self;
}
