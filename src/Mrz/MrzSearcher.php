<?php

namespace Werk365\IdentityDocuments\Mrz;

use Werk365\IdentityDocuments\Helpers\IdCheck;

class MrzSearcher extends Mrz
{
    public function search(string $string): ?string
    {
        [$strippedString, $characters] = $this->stripString($string);
        $keysPositions = $this->findKeysInCharacters($this->keys, $characters);
        $startPosition = $this->findMrzStartPosition($keysPositions, $characters);

        if ($startPosition === null) {
            return null;
        }

        $mrz = $this->getMrz($strippedString, $startPosition);

        return $mrz;
    }

    private function getMrz($strippedString, $startPosition)
    {
        return substr($strippedString, $startPosition, $this->{$this->type}['length']);
    }

    private function findKeysInCharacters(array $keys, array $characters, $positions = []): array
    {
        foreach ($keys as $key => $value) {
            $positions[$key] = array_keys($characters, $key, true);
        }

        return $positions;
    }

    private function canBeCheckDigit(array $characters, int $checkDigitPosition): bool
    {
        if (! isset($characters[$checkDigitPosition])) {
            return false;
        }
        if (! is_numeric($characters[$checkDigitPosition])) {
            return $characters[$checkDigitPosition] === 'O';
        }

        return true;
    }

    private function buildCheckString(array $checkOver, int $position, array $characters, bool $convert = false): string
    {
        $checkStringArray = [];
        foreach ($checkOver as $check) {
            $start = $position + $check[0];
            $end = $start + $check[1] - 1;
            $checkStringArray = array_merge($checkStringArray, range($start, $end));
        }
        $checkString = '';
        foreach ($checkStringArray as $character) {
            $checkString .= ($characters[$character] === 'O' && $convert) ? '0' : $characters[$character];
        }

        return $checkString;
    }

    private function checkPositionInFormat(int $position, array $characters, array $checkDigits)
    {
        foreach ($checkDigits as $checkDigitIndex => $checkOver) {
            $checkDigitPosition = $position + $checkDigitIndex;

            if (! $this->canBeCheckDigit($characters, $checkDigitPosition)) {
                return false;
            }

            $checkDigit = ($characters[$checkDigitPosition] === 'O') ? '0' : $characters[$checkDigitPosition];

            $checkString = $this->buildCheckString($checkOver, $position, $characters);

            if (! IdCheck::checkDigit($checkString, $checkDigit)) {
                $checkString = $this->buildCheckString($checkOver, $position, $characters, true);
                if (! IdCheck::checkDigit($checkString, $checkDigit)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function testPositions(array $template, array $positions, $characters): ?int
    {
        foreach ($positions as $position) {
            if ($this->checkPositionInFormat($position, $characters, $template)) {
                return $position;
            }
        }

        return null;
    }

    private function testKeyTemplates(string $key, array $positions, array $characters): ?int
    {
        foreach ($this->keys[$key] as $name => $template) {
            $position = $this->testPositions($template, $positions, $characters);
            if ($position !== null) {
                $this->type = $name;

                return $position;
            }
        }

        return null;
    }

    private function findMrzStartPosition(array $mrzKeys, array $characters): ?int
    {
        foreach ($mrzKeys as $key => $positions) {
            $position = $this->testKeyTemplates($key, $positions, $characters);
            if ($position) {
                return $position;
            }
        }

        return null;
    }

    private function stripString(string $string): array
    {
        $strippedString = preg_replace('/\r\n|\r|\n/', '', $string);
        $strippedString = preg_replace('/\s+/', '', $strippedString);
        $strippedString = (is_string($strippedString)) ? $strippedString : $string;
        $characters = str_split($strippedString);

        return [$strippedString, $characters];
    }
}
