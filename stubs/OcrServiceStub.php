<?php

namespace DummyNamespace;

use Intervention\Image\Image;
use Werk365\IdentityDocuments\Interfaces\OCR;
use Werk365\IdentityDocuments\Responses\OcrResponse;

class DummyClass implements OCR
{
    public function ocr(Image $image): OcrResponse
    {
        // TODO: Add OCR and return text
        return new OcrResponse('Response text');
    }
}
