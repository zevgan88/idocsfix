<?php

namespace Werk365\IdentityDocuments\Viz;

class VizParser extends Viz
{
    private array $viz = [
        'first_name' => [],
        'last_name' => null,
        'document_number' => null,
    ];

    public function match(array $parsed, string $mrz, string $text): array
    {
        if (! $mrz) {
            return [];
        }
        $ignore = "\n*\s*";
        $mrzCharacters = str_split($mrz);
        foreach ($mrzCharacters as $key => $character) {
            $mrzCharacters[$key] = $character.$ignore;
        }
        $mrzRegex = implode($mrzCharacters);
        $text = preg_replace("/$mrzRegex/", '', $text);
        $text = preg_replace("/\n/", ' ', $text);
        $words = explode(' ', $text);
        $lastNameScore = 0.4;
        $firstNameScore = [];
        foreach ($words as $wordKey => $word) {
            $lastName = $word;
            if (substr_count($parsed['last_name'], '<')) {
                $fillerAmount = range(0, substr_count($parsed['last_name'], '<'));
                $lastName = [];
                foreach ($fillerAmount as $count) {
                    if (isset($words[$wordKey + $count])) {
                        array_push($lastName, $words[$wordKey + $count]);
                    }
                }
                foreach ($lastName as $name) {
                    if (substr_count($name, '-')) {
                        array_pop($lastName);
                    }
                }
                $lastName = implode('<', $lastName);
            }
            if ($this->compare($parsed['last_name'], $lastName) > $lastNameScore) {
                $this->viz['last_name']['value'] = preg_replace('/</', ' ', $lastName);
                $lastNameScore = $this->compare($parsed['last_name'], $lastName);
                $this->viz['last_name']['confidence'] = $lastNameScore;
            }
            if (strpos($word, preg_replace('/</', '', $parsed['document_number'])) !== false) {
                $this->viz['document_number']['value'] = $word;
            }
            foreach ($parsed['first_name'] as $key => $first_name) {
                if (! isset($firstNameScore[$key])) {
                    $firstNameScore[$key] = 0.4;
                }
                if ($this->compare($parsed['first_name'][$key], $word) > $firstNameScore[$key]) {
                    $firstNameScore[$key] = $this->compare($parsed['first_name'][$key], $word);
                    $first_name = [
                        'value' => $word,
                        'confidence' => $firstNameScore[$key],
                    ];
                    $this->viz['first_name'][$key] = $first_name;
                }
            }
        }
        ksort($this->viz['first_name']);
        $this->viz['first_name'] = array_values($this->viz['first_name']);

        return $this->viz;
    }

    private function compare(string $mrz, string $viz)
    {
        $viz = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $viz);
        $viz = preg_replace('/([ ]|[-])/', '<', $viz);
        $viz = preg_replace("/\p{P}/u", '', $viz);

        if (strlen($viz) == 0) {
            return 0;
        }

        $distance = levenshtein(strtolower($mrz), strtolower($viz));

        return (strlen($viz) - $distance) / strlen($viz);
    }
}
