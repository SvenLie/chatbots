<?php

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'Chatbots',
    'settings',
    '',
    'bottom',
    [],
    [
        'access' => 'admin',
        'icon' => 'EXT:chatbots/Resources/Public/Icons/LogoWhite.svg',
        'labels' => 'LLL:EXT:chatbots/Resources/Private/Language/Backend/locallang_mod.xlf',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'Chatbots',
    'settings',
    'overview',
    'top',
    [
        \SvenLie\Chatbots\Controller\Backend\OverviewController::class => 'index, startTraining, activateModel, deleteModel'
    ],
    [
        'access' => 'admin',
        'icon' => 'EXT:chatbots/Resources/Public/Icons/LogoOverviewFramed.svg',
        'labels' => 'LLL:EXT:chatbots/Resources/Private/Language/Backend/locallang_mod_overview.xlf'
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'Chatbots',
    'settings',
    'training_data',
    'after:overview',
    [
        \SvenLie\Chatbots\Controller\Backend\TrainingDataController::class => 'index, delete, showUpdate, update, showAdd, add'
    ],
    [
        'access' => 'admin',
        'icon' => 'EXT:chatbots/Resources/Public/Icons/LogoTrainingDataFramed.svg',
        'labels' => 'LLL:EXT:chatbots/Resources/Private/Language/Backend/locallang_mod_training_data.xlf'
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'Chatbots',
    'settings',
    'responses',
    'after:training_data',
    [
        \SvenLie\Chatbots\Controller\Backend\ResponseController::class => 'index, delete, showUpdate, update, showAdd, add'
    ],
    [
        'access' => 'admin',
        'icon' => 'EXT:chatbots/Resources/Public/Icons/LogoResponseFramed.svg',
        'labels' => 'LLL:EXT:chatbots/Resources/Private/Language/Backend/locallang_mod_response.xlf'
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'Chatbots',
    'settings',
    'stories',
    'after:responses',
    [
        \SvenLie\Chatbots\Controller\Backend\StoryController::class => 'index, delete, showUpdate, update, after, showAdd, add'
    ],
    [
        'access' => 'admin',
        'icon' => 'EXT:chatbots/Resources/Public/Icons/LogoStoryFramed.svg',
        'labels' => 'LLL:EXT:chatbots/Resources/Private/Language/Backend/locallang_mod_story.xlf'
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'Chatbots',
    'settings',
    'rules',
    'after:stories',
    [
        \SvenLie\Chatbots\Controller\Backend\RuleController::class => 'index, delete, showUpdate, update, after, showAdd, add'
    ],
    [
        'access' => 'admin',
        'icon' => 'EXT:chatbots/Resources/Public/Icons/LogoRuleFramed.svg',
        'labels' => 'LLL:EXT:chatbots/Resources/Private/Language/Backend/locallang_mod_rule.xlf'
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'Chatbots',
    'settings',
    'configuration',
    'bottom',
    [
        \SvenLie\Chatbots\Controller\Backend\ConfigurationController::class => 'index, save'
    ],
    [
        'access' => 'admin',
        'icon' => 'EXT:chatbots/Resources/Public/Icons/LogoConfigurationFramed.svg',
        'labels' => 'LLL:EXT:chatbots/Resources/Private/Language/Backend/locallang_mod_configuration.xlf'
    ]
);
