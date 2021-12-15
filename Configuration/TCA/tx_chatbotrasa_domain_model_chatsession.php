<?php
return [
    'ctrl' => [
        'hideTable' => 'true'
    ],
    'columns' => [
        'sender_token' => [
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
            ],
        ],
        'access_token' => [
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
            ],
        ],
        'timestamp' => [
            'config' => [
                'type' => 'text',
                'eval' => 'trim'
            ]
        ]
    ],
];
