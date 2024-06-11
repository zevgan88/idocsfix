<?php

namespace Werk365\IdentityDocuments;

use Exception;
use Intervention\Image\Image;
use ReflectionClass;
use Werk365\IdentityDocuments\Exceptions\CouldNotSetService;
use Werk365\IdentityDocuments\Filters\MergeFilter;
use Werk365\IdentityDocuments\Interfaces\FaceDetection;
use Werk365\IdentityDocuments\Interfaces\OCR;

class IdentityImage
{
// zevgan 5
    public Image $image;
    public Exception $error;
    public string $text;
    public ?Image $face;
    private string $ocrService;
    private string $faceDetectionService;

    public function __construct(Image $image, $ocrService, $faceDetectionService)
    {
        $this->setOcrService($ocrService);
        $this->setFaceDetectionService($faceDetectionService);
        $this->setImage($image);
    }

    public function setOcrService(string $service)
    {
        $class = new ReflectionClass($service);
        if (! $class->implementsInterface(OCR::class)) {
            throw CouldNotSetService::couldNotDetectInterface(OCR::class, $service);
        }
        $this->ocrService = $service;
    }

    public function setFaceDetectionService(string $service)
    {
        $class = new ReflectionClass($service);
        if (! $class->implementsInterface(FaceDetection::class)) {
            throw CouldNotSetService::couldNotDetectInterface(FaceDetection::class, $service);
        }
        $this->faceDetectionService = $service;
    }

    public function setImage(Image $image)
    {
        $this->image = $image;
    }

    public function merge(IdentityImage $image): IdentityImage
    {
        return new IdentityImage($this->image->filter(new MergeFilter($image->image)), $this->ocrService, $this->faceDetectionService);
    }

    public function ocr(): string
    {
        $service = new $this->ocrService();

        return $this->text = $service->ocr($this->image)->text;
    }

    public function face(): ?Image
    {
        $service = new $this->faceDetectionService();

        return $this->face = $service->detect($this);
    }
}
