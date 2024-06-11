<?php

namespace Werk365\IdentityDocuments\Tests\Feature;

use Tests\TestCase;
use Werk365\IdentityDocuments\Mrz\MrzParser;

class MrzParserTest extends TestCase
{
    private string $mrz = 'P<NLDDE<BRUIJN<<WILLEKE<LISELOTTE<<<<<<<<<<<SPECI20142NLD6503101F2401151999999990<<<<<82';
    private array $expected = [
        'document_key' => 'P',
        'document_type' => '<',
        'issuing_country' => 'NLD',
        'full_name' => 'DE<BRUIJN<<WILLEKE<LISELOTTE<<<<<<<<<<<',
        'document_number' => 'SPECI2014',
        'nationality' => 'NLD',
        'date_of_birth' => '650310',
        'sex' => 'F',
        'expiration_date' => '240115',
        'personal_number' => '999999990<<<<<',
        'optional_data_row_1' => null,
        'optional_data_row_2' => null,
        'last_name' => 'DE<BRUIJN',
        'first_name' => [
            0 => 'WILLEKE',
            1 => 'LISELOTTE',
        ],
        'issuing_country_name' => 'Netherlands',
        'nationality_name' => 'Netherlands',
    ];

    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function correctly_parse_mrz()
    {
        $parser = new MrzParser();
        $this->assertEquals($this->expected, $parser->parse($this->mrz, 'TD3'));
    }
}
