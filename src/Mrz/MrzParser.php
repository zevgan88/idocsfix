<?php

namespace Werk365\IdentityDocuments\Mrz;

class MrzParser extends Mrz
{
    public function parse($mrz, $type)
    {
        $parsed = [];

        if (! isset($type) || ! $type) {
            return $parsed;
        }

        foreach ($this->values as $name=>$value) {
            if ($value[$type]) {
                $parsed[$name] = substr($mrz, ...$value[$type]);
            } else {
                $parsed[$name] = null;
            }
        }

        [$parsed['last_name'], $parsed['first_name']] = $this->getFirstLastName($parsed['full_name']);

        $parsed['issuing_country_name'] = $this->getFullCountryName($parsed['issuing_country']);
        $parsed['nationality_name'] = $this->getFullCountryName($parsed['issuing_country']);

        return $parsed;
    }

    private function getFirstLastName(string $fullName): array
    {
        [$lastName, $firstName] = explode('<<', $fullName);

        return [$lastName, explode('<', $firstName)];
    }

    private function getFullCountryName($countryCode)
    {
        $countryCode = preg_replace('/</', '', $countryCode);

        return $this->countries[$countryCode] ?? null;
    }
}
