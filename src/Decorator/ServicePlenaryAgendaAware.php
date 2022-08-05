<?php

namespace App\Decorator;

use App\Service\PlenaryAgenda;

interface ServicePlenaryAgendaAware
{
    public function setPlenaryAgendaService(PlenaryAgenda $plenaryAgenda): self;
}
