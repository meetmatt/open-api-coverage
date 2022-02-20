<?php

use MeetMatt\OpenApiSpecCoverage\Util\Util;

require_once __DIR__ . '/vendor/autoload.php';

$spec = [
    'driver'         => 'Speedy Racer',
    'uncovered_key'  => 'someone said coverage?',
    'different_type' => 1,
    'car'            => [
        'engine'       => [
            'displacement' => 6.5
        ],
        'transmission' => [
            'is_automatic' => false
        ],
        'wheels'       => [
            ['size' => 21, 'type' => 'Pirelli P-Zero'],
            ['size' => 21, 'type' => 'Pirelli P-Zero'],
        ],
        'tags' => [
            'fast',
            'sport',
            'nested' => [
                'foo',
                'bar'
            ]
        ],
    ],
];

$coverage = [
    'driver'         => 'Speedy Race',
    'new_key'        => 'yeah',
    'different_type' => '1',
    'car'            => [
        'engine'       => [
            'displacement' => 2.0
        ],
        'transmission' => [
            'is_automatic' => true,
            'is_broken'    => true,
        ],
        'wheels'       => [
            ['size' => 21, 'type' => 'Pirelli P-Zero'],
            ['size' => 21, 'type' => 'Pirelli P-Zero'],
            ['size' => 21, 'type' => 'A new wheel'],
        ],
        'tags' => [
            'luxury',
            'fancy',
            'fast'
        ]
    ],
];

echo json_encode([
    'spec' => Util::flatten($spec),
    'cvrg' => Util::flatten($coverage),
]);

//echo json_encode(Util::diff($spec, $coverage));
