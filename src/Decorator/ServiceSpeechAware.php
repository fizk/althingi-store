<?php

namespace App\Decorator;

use App\Service\Speech;

interface ServiceSpeechAware
{
    public function setSpeechService(Speech $speech): self;
}
