<?php

return [
    'frontend' => [
        'svenlie/chatbots/ajax-routes' => [
            'target' => \SvenLie\Chatbots\Middleware\AjaxRoutes::class,
            'before' => [
                'typo3/cms-frontend/base-redirect-resolver',
            ],
            'after' => [
                'typo3/cms-frontend/site',
            ],
        ]
    ]
];
