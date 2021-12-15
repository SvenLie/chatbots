<?php

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'SvenLie.ChatbotRasa',
    'Chatbot',
    [
        'Chatbot' => 'index',
    ],
    // non-cacheable actions
    [
        'Chatbot' => 'index',
    ],
);

// not needed at v10 and v11
$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    \TYPO3\CMS\Core\Imaging\IconRegistry::class
);
$iconRegistry->registerIcon(
    'actions-arrow-down-left', // Icon-Identifier, z.B. tx-myext-action-preview
    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
    ['source' => 'EXT:chatbot_rasa/Resources/Public/Icons/actions-arrow-down-left.svg']
);
