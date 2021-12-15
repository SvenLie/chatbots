<?php

namespace SvenLie\ChatbotRasa\Controller\Backend;

use SvenLie\ChatbotRasa\Utility\ExtensionConfigurationUtility;
use SvenLie\ChatbotRasa\Utility\RasaApiUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class OverviewController extends Controller
{

    protected string $controllerName = 'Backend\Overview';

    protected string $moduleName = 'chatbotRasaChatbot_ChatbotRasaOverview';

    protected ExtensionConfigurationUtility $extensionConfigurationUtility;

    protected ExtensionConfiguration $extensionConfiguration;

    public function __construct(ExtensionConfigurationUtility $extensionConfigurationUtility, ExtensionConfiguration $extensionConfiguration)
    {
        $this->extensionConfigurationUtility = $extensionConfigurationUtility;
        $this->extensionConfiguration = $extensionConfiguration;
        parent::__construct($extensionConfiguration);
    }


    public function indexAction()
    {
        $isValid = $this->extensionConfigurationUtility->isExtensionConfigurationValid();

        if (!$isValid) {
            $this->addFlashMessage(
                LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod.xlf:extension_configuration_not_filled'),
                '',
                AbstractMessage::ERROR
            );
        } else {
            $rasaUrl = $this->extensionConfiguration->get('chatbot_rasa', 'rasaUrl');
            $this->rasaApiUtility = new RasaApiUtility($rasaUrl);

            $accessToken = $this->authenticate($this->rasaApiUtility);

            if ($accessToken) {
                $healthChecks = $this->rasaApiUtility->getHealthStatus($accessToken);
                $trainedModels = $this->rasaApiUtility->getTrainedModels($accessToken);
                $this->view->assign('healthChecks', $healthChecks);
                $this->view->assign('trainedModels', $trainedModels);
            } else {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod.xlf:connection_not_working'),
                    '',
                    AbstractMessage::ERROR
                );
                $isValid = false;
            }
        }

        $this->view->assign('isValid', $isValid);
    }

    public function startTrainingAction()
    {
        $isValid = $this->extensionConfigurationUtility->isExtensionConfigurationValid();

        if (!$isValid) {
            $this->addFlashMessage(
                LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod.xlf:extension_configuration_not_filled'),
                '',
                AbstractMessage::ERROR
            );
        } else {
            $rasaUrl = $this->extensionConfiguration->get('chatbot_rasa', 'rasaUrl');
            $this->rasaApiUtility = new RasaApiUtility($rasaUrl);

            $accessToken = $this->authenticate($this->rasaApiUtility);

            $response = $this->rasaApiUtility->trainModel($accessToken);

            if ($response) {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_overview.xlf:training_started'),
                    '',
                    AbstractMessage::OK
                );
            } else {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_overview.xlf:training_not_started'),
                    '',
                    AbstractMessage::ERROR
                );
            }
        }

        $this->redirect('index');
    }

    public function activateModelAction()
    {
        if ($this->request->hasArgument('modelName')) {
            $modelName = $this->request->getArgument('modelName');
            $rasaUrl = $this->extensionConfiguration->get('chatbot_rasa', 'rasaUrl');
            $this->rasaApiUtility = new RasaApiUtility($rasaUrl);

            $accessToken = $this->authenticate($this->rasaApiUtility);

            $isModelMarkedAsActive = $this->rasaApiUtility->markModelAsActive($accessToken, $modelName);

            if ($isModelMarkedAsActive) {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_overview.xlf:model_activated'),
                    '',
                    AbstractMessage::OK
                );
            } else {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_overview.xlf:model_not_activated'),
                    '',
                    AbstractMessage::ERROR
                );
            }


        } else {
            $this->addFlashMessage(
                LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_overview.xlf:model_not_activated'),
                '',
                AbstractMessage::ERROR
            );
        }
        $this->redirect('index');
    }

    public function deleteModelAction()
    {
        if ($this->request->hasArgument('modelName')) {
            $modelName = $this->request->getArgument('modelName');
            $rasaUrl = $this->extensionConfiguration->get('chatbot_rasa', 'rasaUrl');
            $this->rasaApiUtility = new RasaApiUtility($rasaUrl);

            $accessToken = $this->authenticate($this->rasaApiUtility);

            $isModelDeleted = $this->rasaApiUtility->deleteModel($accessToken, $modelName);

            if ($isModelDeleted) {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod.xlf:model_deleted'),
                    '',
                    AbstractMessage::OK
                );
            } else {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod.xlf:model_not_deleted'),
                    '',
                    AbstractMessage::ERROR
                );
            }


        } else {
            $this->addFlashMessage(
                LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod.xlf:model_not_deleted'),
                '',
                AbstractMessage::ERROR
            );
        }
        $this->redirect('index');
    }

}
