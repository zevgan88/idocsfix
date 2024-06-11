<?php

namespace DummyNamespace;

use Intervention\Image\Image;
use Werk365\IdentityDocuments\IdentityImage;
use Werk365\IdentityDocuments\Interfaces\FaceDetection;
use Werk365\IdentityDocuments\Interfaces\OCR;
use Werk365\IdentityDocuments\Responses\OcrResponse;

class DummyClass implements OCR, FaceDetection
{
    public function ocr(Image $image): OcrResponse
    {
        // TODO: Add OCR and return text
        return new OcrResponse('Response text');
    }

    public function detect(IdentityImage $image): ?Image
    {
        // TODO: Add face detection and return image of face
        return new Image();
    }
}
