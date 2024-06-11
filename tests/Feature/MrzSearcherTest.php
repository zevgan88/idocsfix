<?php

namespace Werk365\IdentityDocuments\Tests\Feature;

use Tests\TestCase;
use Werk365\IdentityDocuments\Mrz\MrzSearcher;

class MrzSearcherTest extends TestCase
{
    private string $mrz = 'P<NLDDE<BRUIJN<<WILLEKE<LISELOTTE<<<<<<<<<<<SPECI20142NLD6503101F2401151999999990<<<<<82';
    private string $full_text = 'PASPOORT PASSPORT PASSEPORT O KONINKRIJK DER NEDERLANDEN KINGDOM OF THE NETHERLANDS ROYAUME DES PAYSBAS P NLD Nederlandse SPECI2014 De Bruijn e/v Molenaar Willeke Liselotte 10 MAA/MAR 1965  Specimen V/F 1,75 m dm ve wwwd 15 JAN/JAN 2014 15 JAN/JAN 2024 1935 Burg. van Stad en Dorp w.L. de 3ujn P<NLDDE<BRUIJN<<WILLEKE<LISELOTTE<<<<<<<<<<< SPECI20142NLD6503101F2401151999999990<<<<<82';
    private string $malformed_text = 'PASPOORT PASSPORT PASSEPORT O KONINKRIJK DER NEDERLANDEN KINGDOM OF THE NETHERLANDS ROYAUME DES PAYSBAS P NLD Nederlandse SPECI2014 De Bruijn e/v Molenaar Willeke Liselotte 10 MAA/MAR 1965  Specimen V/F 1,75 m dm ve wwwd 15 JAN/JAN 2014 15 JAN/JAN 2024 1935 Burg. van Stad en Dorp w.L. de 3ujn P<NLDDE<BRUIJN<<WILLEKE<LISELOTTE<<<<<<<<<<< SPECI20142NLD4503101F2401151999999990<<<<<82';

    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function correct_mrz_is_found_in_text()
    {
        $searcher = new MrzSearcher();
        $this->assertEquals($this->mrz, $searcher->search($this->full_text));
    }

    /** @test */
    public function malformed_mrz_is_not_found_in_text()
    {
        $searcher = new MrzSearcher();
        $this->assertEquals(null, $searcher->search($this->malformed_text));
    }
}
