<?php

namespace App\Decorator;

use App\Service\Party;

interface ServicePartyAware
{
    public function setPartyService(Party $party): self;
}
