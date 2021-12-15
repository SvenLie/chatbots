<?php

namespace SvenLie\ChatbotRasa\Utility;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class ExtensionConfigurationUtility
{
    /**
     * @var ObjectManager $objectManager
     */
    protected ObjectManager $objectManager;
    protected ExtensionConfiguration $extensionConfiguration;
    protected array $CONFIGURATION_VALUES = [
        'rasaUrl', 'rasaUsername', 'rasaPassword'
    ];

    public function __construct()
    {
        /*
         * Deprecated in v11
         */
        $this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $this->extensionConfiguration = $this->objectManager->get(ExtensionConfiguration::class);
    }

    public function isExtensionConfigurationValid(): bool
    {
        foreach ($this->CONFIGURATION_VALUES as $CONFIGURATION_VALUE) {
            if (empty($this->extensionConfiguration->get('chatbot_rasa', $CONFIGURATION_VALUE))) {
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
