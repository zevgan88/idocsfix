<?php

namespace Werk365\IdentityDocuments\Interfaces;

use Intervention\Image\Image;
use Werk365\IdentityDocuments\IdentityImage;

interface FaceDetection
{
    public function detect(IdentityImage $image): ?Image;
}
