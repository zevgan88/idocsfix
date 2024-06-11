<?php

namespace DummyNamespace;

use Intervention\Image\Image;
use Werk365\IdentityDocuments\IdentityImage;
use Werk365\IdentityDocuments\Interfaces\FaceDetection;

class DummyClass implements FaceDetection
{
    public function detect(IdentityImage $image): ?Image
    {
        // TODO: Add face detection and return image of face
        return new Image();
    }
}
