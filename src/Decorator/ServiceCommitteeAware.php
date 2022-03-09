<?php

namespace App\Decorator;

use App\Service\Committee;

interface ServiceCommitteeAware
{
    public function setCommitteeService(Committee $committee): self;
}
