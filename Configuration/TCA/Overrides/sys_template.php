<?php

defined('TYPO3') or die('Access denied.');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'chatbot_rasa',
    'Configuration/TypoScript',
    'Chatbot RASA'
);