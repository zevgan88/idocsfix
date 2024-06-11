<?php

namespace Werk365\IdentityDocuments\Interfaces;

use Intervention\Image\Image;
use Werk365\IdentityDocuments\Responses\OcrResponse;

interface OCR
{
    public function ocr(Image $image): OcrResponse;
}
