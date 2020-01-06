<?php

return [
    'Quickstart' => 'docs/quickstart',
    'Overview' => 'docs',
    'Installation' => 'docs/installation',
    'Configuration' => 'docs/configuration',
    'Connecting' => 'docs/connecting',
    'Searching '=> 'docs/searching',
    'Models' => [
        'children' => [
            'Getting Started' => 'docs/models',
            'Relationships' => 'docs/model-relationships',
            'Accessors & Mutators' => 'docs/model-mutators',
        ],
    ],
    'Events' => 'docs/events',
    'Logging' => 'docs/logging',
    'Tutorials' => [
        'children' => [
            'Authentication' => 'docs/tutorials/authentication',
            'Common Queries' => 'docs/tutorials/common-queries',
            'ActiveDirectory' => [
                'children' => [
                    'User Management' => 'docs/tutorials/user-management',
                ],
            ]
        ],
    ],
    'Versioning' => 'docs/versioning',
    'License' => 'docs/license',
];
