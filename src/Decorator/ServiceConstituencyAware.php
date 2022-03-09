<?php

namespace App\Decorator;

use App\Service\Constituency;

interface ServiceConstituencyAware
{
    public function setConstituencyService(Constituency $constituency): self;
}
