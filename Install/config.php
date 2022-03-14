<?php

declare(strict_types=1);

return [
    'name' => 'Office',
    'require' => [
        'hcms_version' => '0.2.0',
        'composer' => [
            'phpoffice/phpspreadsheet' => '^1.22',
            'phpoffice/phpword' => '^0.18.3',
        ],
        'module' => []
    ],
    'version' => '1.0.0'
];