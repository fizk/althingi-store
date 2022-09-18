<?php

namespace App\Decorator;

use App\Service\Document;

interface ServiceDocumentAware
{
    public function setDocumentService(Document $document): self;
}
