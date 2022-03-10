<?php

namespace App\Decorator;

use App\Service\CongressmanSitting;

interface ServiceCongressmanSittingAware
{
    public function setCongressmanSittingService(CongressmanSitting $congressmanSitting): self;
}
