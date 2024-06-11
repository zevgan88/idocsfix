<?php

namespace Werk365\IdentityDocuments\Services;

use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Intervention\Image\Image;
use Werk365\IdentityDocuments\IdentityImage;
use Werk365\IdentityDocuments\Interfaces\FaceDetection;
use Werk365\IdentityDocuments\Interfaces\OCR;
use Werk365\IdentityDocuments\Responses\OcrResponse;

class Google implements OCR, FaceDetection
{
    private ImageAnnotatorClient $annotator;
    private array $credentials;

    public function __construct()
    {
        $this->credentials = config('google_key');
        $this->annotator = new ImageAnnotatorClient(
            ['credentials' => $this->credentials]
        );
    }

    public function ocr(Image $image): OcrResponse
    {
        $response = $this->annotator->textDetection((string) $image->encode());
        $text = $response->getTextAnnotations();

        return new OcrResponse($text[0]->getDescription());
    }

    public function detect(IdentityImage $image): ?Image
    {
        $response = $this->annotator->faceDetection((string) $image->image->encode());
        $largest = 0;
        $largestFace = null;
        foreach ($response->getFaceAnnotations() as $key => $face) {
            $dimensions = $this->getFaceDimensions($face);
            if ($dimensions['width'] + $dimensions['height'] > $largest) {
                $largest = $dimensions['width'] + $dimensions['height'];
                $largestFace = $dimensions;
            }
        }
        if (! $largestFace) {
            return null;
        }
        $face = $image->image;
        $face->resizeCanvas($largestFace['centerX'] * 2, $largestFace['centerY'] * 2, 'top-left');
        $face->rotate($largestFace['roll']);
        $face->resizeCanvas($largestFace['width'], $largestFace['height'], 'center');

        return $face;
    }

    private function getFaceDimensions($face)
    {
        $rectangle = [];
        $roll = $face->getRollAngle();

        foreach ($face->getBoundingPoly()->getVertices() as $key => $vertex) {
            $rectangle[$key] = [];
            $rectangle[$key]['x'] = $vertex->getX();
            $rectangle[$key]['y'] = $vertex->getY();
        }

        $rectangle['width'] = $rectangle[1]['x'] - $rectangle[0]['x'];
        $rectangle['height'] = $rectangle[3]['y'] - $rectangle[0]['y'];
        $rectangle['centerX'] = $rectangle[0]['x'] + $rectangle['width'] / 2;
        $rectangle['centerY'] = $rectangle[0]['y'] + $rectangle['height'] / 2;
        $rectangle['roll'] = $roll;

        return $rectangle;
    }
}
