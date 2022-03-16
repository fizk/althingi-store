<?php

namespace App\Decorator;

use App\Service\CommitteeSitting;

interface ServiceCommitteeSittingAware
{
    public function setCommitteeSittingService(CommitteeSitting $committeeSitting): self;
}
