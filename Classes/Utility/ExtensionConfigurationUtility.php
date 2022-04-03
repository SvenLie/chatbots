<?php

namespace SvenLie\Chatbots\Utility;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ExtensionConfigurationUtility
{
    protected ExtensionConfiguration $extensionConfiguration;
    protected array $CONFIGURATION_VALUES = [
        'rasaUrl', 'rasaUsername', 'rasaPassword'
    ];

    public function __construct()
    {
        $this->extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
    }

    public function isExtensionConfigurationValid(): bool
    {
        foreach ($this->CONFIGURATION_VALUES as $CONFIGURATION_VALUE) {
            if (empty($this->extensionConfiguration->get('chatbots', $CONFIGURATION_VALUE))) {
                return false;
            }
        }

        return true;
    }

    public function getExtensionConfiguration(): ExtensionConfiguration
    {
        return $this->extensionConfiguration;
    }

}
