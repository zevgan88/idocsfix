<?php

namespace Werk365\IdentityDocuments\Responses;

class OcrResponse
{
    public string $text;

    public function __construct(string $input)
    {
        $this->text = $input;
    }
}
