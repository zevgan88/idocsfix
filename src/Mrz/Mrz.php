<?php

namespace Werk365\IdentityDocuments\Mrz;

class Mrz
{
    protected array $TD1;
    protected array $TD2;
    protected array $TD3;
    protected array $MRVA;
    protected array $MRVB;
    protected array $keys;
    protected array $values;
    protected array $countries;
    public string $type = '';

    public function __construct()
    {
        $this->countries = config('id_countries');
        $this->loadConfig();
    }

    private function loadConfig()
    {
        $this->TD3 = [
            'checks' => [
                53 => [[44, 9]],
                63 => [[57, 6]],
                71 => [[65, 6]],
                86 => [[72, 14]],
                87 => [[44, 10], [57, 7], [65, 22]],
            ],
            'length' => 88,
        ];

        $this->TD2 = [
            'checks' => [
                45 => [[36, 9]],
                55 => [[49, 6]],
                64 => [[58, 6]],
                71 => [[44, 10], [49, 7], [58, 12]],
            ],
            'length' => 72,
        ];

        $this->TD1 = [
            'checks' => [
                14 => [[5, 9]],
                36 => [[30, 6]],
                44 => [[38, 6]],
                59 => [[5, 25], [30, 7], [38, 7], [48, 10]],
            ],
            'length' => 90,
        ];

        $this->MRVA = [
            'checks' => [
                53 => [[44, 9]],
                63 => [[57, 6]],
                71 => [[65, 6]],
                86 => [[72, 14]],
                87 => [[44, 10], [57, 7], [65, 22]],
            ],
            'length' => 88,
        ];

        $this->MRVB = [
            'checks' => [
                53 => [[44, 9]],
                63 => [[57, 6]],
                71 => [[65, 6]],
                86 => [[72, 14]],
                87 => [[44, 10], [57, 7], [65, 22]],
            ],
            'length' => 88,
        ];

        $this->keys = [
            'P' => ['TD3' => &$this->TD3['checks']],
            'I' => ['TD1' => &$this->TD1['checks'], 'TD2' => &$this->TD2['checks']],
            'A' => ['TD1' => &$this->TD1['checks'], 'TD2' => &$this->TD2['checks']],
            'C' => ['TD1' => &$this->TD1['checks'], 'TD2' => &$this->TD2['checks']],
            'V' => ['MRVA' => &$this->MRVA['checks'], 'MRVB' => &$this->MRVB['checks']],
        ];

        // values to be found in MRZ by format, [position in string, sub string length]
        $this->values = [
            'document_key' => [
                'TD1' => [0, 1],
                'TD2' => [0, 1],
                'TD3' => [0, 1],
                'MRVA' => [0, 1],
                'MRVB' => [0, 1],
            ],
            'document_type' => [
                'TD1' => [1, 1],
                'TD2' => [1, 1],
                'TD3' => [1, 1],
                'MRVA' => [1, 1],
                'MRVB' => [1, 1],
            ],
            'issuing_country' => [
                'TD1' => [2, 3],
                'TD2' => [2, 3],
                'TD3' => [2, 3],
                'MRVA' => [2, 3],
                'MRVB' => [2, 3],
            ],
            'full_name' => [
                'TD1' => [60, 30],
                'TD2' => [5, 31],
                'TD3' => [5, 39],
                'MRVA' => [5, 39],
                'MRVB' => [5, 31],
            ],
            'document_number' => [
                'TD1' => [5, 9],
                'TD2' => [36, 9],
                'TD3' => [44, 9],
                'MRVA' => [44, 9],
                'MRVB' => [36, 9],
            ],
            'nationality' => [
                'TD1' => [45, 3],
                'TD2' => [46, 3],
                'TD3' => [54, 3],
                'MRVA' => [54, 3],
                'MRVB' => [46, 3],
            ],
            'date_of_birth' => [
                'TD1' => [30, 6],
                'TD2' => [49, 6],
                'TD3' => [57, 6],
                'MRVA' => [57, 6],
                'MRVB' => [49, 6],
            ],
            'sex' => [
                'TD1' => [37, 1],
                'TD2' => [56, 1],
                'TD3' => [64, 1],
                'MRVA' => [64, 1],
                'MRVB' => [56, 1],
            ],
            'expiration_date' => [
                'TD1' => [38, 6],
                'TD2' => [57, 6],
                'TD3' => [65, 6],
                'MRVA' => [65, 6],
                'MRVB' => [57, 6],
            ],
            'personal_number' => [
                'TD1' => [],
                'TD2' => [],
                'TD3' => [72, 14],
                'MRVA' => [],
                'MRVB' => [],
            ],
            'optional_data_row_1' => [
                'TD1' => [15, 15],
                'TD2' => [],
                'TD3' => [],
                'MRVA' => [],
                'MRVB' => [],
            ],
            'optional_data_row_2' => [
                'TD1' => [48, 11],
                'TD2' => [64, 7],
                'TD3' => [],
                'MRVA' => [72, 16],
                'MRVB' => [64, 8],
            ],
        ];
    }
}
