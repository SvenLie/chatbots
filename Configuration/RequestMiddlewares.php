<?php

return [
    'frontend' => [
        'svenlie/chatbot_rasa/ajax-routes' => [
            'target' => \SvenLie\ChatbotRasa\Middleware\AjaxRoutes::class,
            'before' => [
                'typo3/cms-frontend/base-redirect-resolver',
            ],
            'after' => [
                'typo3/cms-frontend/site',
            ],
        ]
    ]
];
