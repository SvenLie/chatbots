<?php

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'SvenLie.ChatbotRasa',
    'chatbot',
    '',
    'bottom',
    [],
    [
        'access' => 'admin',
        'icon' => 'EXT:chatbot_rasa/Resources/Public/Icons/LogoWhite.svg',
        'labels' => 'LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod.xlf',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'SvenLie.ChatbotRasa',
    'chatbot',
    'overview',
    'top',
    [
        \SvenLie\ChatbotRasa\Controller\Backend\OverviewController::class => 'index, startTraining, activateModel, deleteModel'
    ],
    [
        'access' => 'admin',
        'icon' => 'EXT:chatbot_rasa/Resources/Public/Icons/LogoOverviewFramed.svg',
        'labels' => 'LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_overview.xlf'
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'SvenLie.ChatbotRasa',
    'chatbot',
    'training_data',
    'after:overview',
    [
        \SvenLie\ChatbotRasa\Controller\Backend\TrainingDataController::class => 'index, delete, showUpdate, update, showAdd, add'
    ],
    [
        'access' => 'admin',
        'icon' => 'EXT:chatbot_rasa/Resources/Public/Icons/LogoTrainingDataFramed.svg',
        'labels' => 'LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_training_data.xlf'
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'SvenLie.ChatbotRasa',
    'chatbot',
    'responses',
    'after:training_data',
    [
        \SvenLie\ChatbotRasa\Controller\Backend\ResponseController::class => 'index, delete, showUpdate, update, showAdd, add'
    ],
    [
        'access' => 'admin',
        'icon' => 'EXT:chatbot_rasa/Resources/Public/Icons/LogoResponseFramed.svg',
        'labels' => 'LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_response.xlf'
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'SvenLie.ChatbotRasa',
    'chatbot',
    'stories',
    'after:responses',
    [
        \SvenLie\ChatbotRasa\Controller\Backend\StoryController::class => 'index, delete, showUpdate, update, after, showAdd, add'
    ],
    [
        'access' => 'admin',
        'icon' => 'EXT:chatbot_rasa/Resources/Public/Icons/LogoStoryFramed.svg',
        'labels' => 'LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_story.xlf'
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'SvenLie.ChatbotRasa',
    'chatbot',
    'rules',
    'after:stories',
    [
        \SvenLie\ChatbotRasa\Controller\Backend\RuleController::class => 'index, delete, showUpdate, update, after, showAdd, add'
    ],
    [
        'access' => 'admin',
        'icon' => 'EXT:chatbot_rasa/Resources/Public/Icons/LogoRuleFramed.svg',
        'labels' => 'LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_rule.xlf'
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'SvenLie.ChatbotRasa',
    'chatbot',
    'configuration',
    'bottom',
    [
        \SvenLie\ChatbotRasa\Controller\Backend\ConfigurationController::class => 'index, save'
    ],
    [
        'access' => 'admin',
        'icon' => 'EXT:chatbot_rasa/Resources/Public/Icons/LogoConfigurationFramed.svg',
        'labels' => 'LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_configuration.xlf'
    ]
);
