<?php

namespace App\Decorator;

use App\Service\Congressman;

interface ServiceCongressmanAware
{
    public function setCongressmanService(Congressman $congressman): self;
}
