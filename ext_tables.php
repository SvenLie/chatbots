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
        'Backend\\Overview' => 'index, startTraining, activateModel, deleteModel'
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
        'Backend\\TrainingData' => 'index, delete, showUpdate, update, showAdd, add'
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
        'Backend\\Response' => 'index, delete, showUpdate, update, showAdd, add'
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
        'Backend\\Story' => 'index, delete, showUpdate, update, after, showAdd, add'
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
        'Backend\\Rule' => 'index, delete, showUpdate, update, after, showAdd, add'
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
        'Backend\\Configuration' => 'index, save'
    ],
    [
        'access' => 'admin',
        'icon' => 'EXT:chatbot_rasa/Resources/Public/Icons/LogoConfigurationFramed.svg',
        'labels' => 'LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_configuration.xlf'
    ]
);
