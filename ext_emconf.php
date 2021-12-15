<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'RASA Chatbot Integration',
    'description' => 'An extension for integrate a chatbot with RASA-Framework.',
    'category' => 'plugin',
    'author' => 'Sven Liebert',
    'author_email' => 'mail@sven-liebert.de',
    'state' => 'beta',
    'clearCacheOnLoad' => true,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-9.5.99',
        ],
    ],
];
