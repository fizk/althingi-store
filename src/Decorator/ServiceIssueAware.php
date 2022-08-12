<?php

namespace App\Decorator;

use App\Service\Issue;

interface ServiceIssueAware
{
    public function setIssueService(Issue $issue): self;
}
