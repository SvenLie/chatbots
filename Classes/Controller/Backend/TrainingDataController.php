<?php

namespace SvenLie\ChatbotRasa\Controller\Backend;

use SvenLie\ChatbotRasa\Domain\Model\TrainingData;
use SvenLie\ChatbotRasa\Utility\ExtensionConfigurationUtility;
use SvenLie\ChatbotRasa\Utility\RasaApiUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\Components\Buttons\LinkButton;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class TrainingDataController extends Controller
{

    protected string $controllerName = 'Backend\TrainingData';

    protected string $moduleName = 'chatbotRasaChatbot_ChatbotRasaTrainingData';

    protected IconFactory $iconFactory;

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
                $this->createAddAndRefreshButtons();
                $trainingData = $this->rasaApiUtility->getTrainingData($accessToken);

                $this->view->assign('trainingData', $trainingData);
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

    public function deleteAction()
    {
        if ($this->request->hasArgument('trainingDataId')) {
            $trainingDataId = $this->request->getArgument('trainingDataId');
            $rasaUrl = $this->extensionConfiguration->get('chatbot_rasa', 'rasaUrl');
            $this->rasaApiUtility = new RasaApiUtility($rasaUrl);

            $accessToken = $this->authenticate($this->rasaApiUtility);

            $isModelDeleted = $this->rasaApiUtility->deleteTrainingData($accessToken, $trainingDataId);

            if ($isModelDeleted) {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_training_data.xlf:training_data_deleted'),
                    '',
                    AbstractMessage::OK
                );
            } else {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_training_data.xlf:training_data_not_deleted'),
                    '',
                    AbstractMessage::ERROR
                );
            }


        } else {
            $this->addFlashMessage(
                LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_training_data.xlf:training_data_not_deleted'),
                '',
                AbstractMessage::ERROR
            );
        }
        $this->redirect('index');
    }

    public function showUpdateAction()
    {
        if ($this->request->hasArgument('trainingDataId') && $this->request->hasArgument('trainingDataText') && $this->request->hasArgument('trainingDataIntent')) {
            $this->createBackButton();
            $trainingDataId = $this->request->getArgument('trainingDataId');
            $trainingDataText = $this->request->getArgument('trainingDataText');
            $trainingDataIntent = $this->request->getArgument('trainingDataIntent');

            $rasaUrl = $this->extensionConfiguration->get('chatbot_rasa', 'rasaUrl');
            $this->rasaApiUtility = new RasaApiUtility($rasaUrl);
            $accessToken = $this->authenticate($this->rasaApiUtility);

            $intents = $this->rasaApiUtility->getIntents($accessToken);

            $this->view->assignMultiple([
                'intents' => $intents,
                'trainingDataId' => $trainingDataId,
                'trainingDataText' => $trainingDataText,
                'trainingDataIntent' => $trainingDataIntent
            ]);

        } else {
            $this->addFlashMessage(
                LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_training_data.xlf:training_data_not_valid'),
                '',
                AbstractMessage::ERROR
            );

            $this->redirect('index');
        }
    }

    public function updateAction()
    {
        if ($this->request->hasArgument('trainingDataId') && !empty($this->request->getArgument('trainingDataId')) && $this->request->hasArgument('trainingDataText') && !empty($this->request->getArgument('trainingDataText')) && $this->request->hasArgument('trainingDataIntent') && !empty($this->request->getArgument('trainingDataIntent'))) {
            $trainingDataId = $this->request->getArgument('trainingDataId');
            $trainingDataText = $this->request->getArgument('trainingDataText');
            $trainingDataIntent = $this->request->getArgument('trainingDataIntent');

            $rasaUrl = $this->extensionConfiguration->get('chatbot_rasa', 'rasaUrl');
            $this->rasaApiUtility = new RasaApiUtility($rasaUrl);
            $accessToken = $this->authenticate($this->rasaApiUtility);

            $trainingData = new TrainingData();
            $trainingData->setId($trainingDataId);
            $trainingData->setIntent($trainingDataIntent);
            $trainingData->setText($trainingDataText);

            $isTrainingDataUpdated = $this->rasaApiUtility->updateTrainingData($accessToken, $trainingData);

            if ($isTrainingDataUpdated) {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_training_data.xlf:training_data_updated'),
                    '',
                    AbstractMessage::OK
                );
                $this->redirect('index');
            } else {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_training_data.xlf:training_data_not_updated'),
                    '',
                    AbstractMessage::ERROR
                );

                $this->redirect('showUpdate', 'Backend\TrainingData', 'ChatbotRasa', ['trainingDataId' => $trainingDataId, 'trainingDataText' => $trainingDataText, 'trainingDataIntent' => $trainingDataIntent]);
            }

        } else {
            $this->addFlashMessage(
                LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_training_data.xlf:training_data_not_valid'),
                '',
                AbstractMessage::ERROR
            );

            $this->redirect('index');
        }
    }

    public function showAddAction()
    {
        $this->createBackButton();
        $rasaUrl = $this->extensionConfiguration->get('chatbot_rasa', 'rasaUrl');
        $this->rasaApiUtility = new RasaApiUtility($rasaUrl);
        $accessToken = $this->authenticate($this->rasaApiUtility);

        $intents = $this->rasaApiUtility->getIntents($accessToken);

        $this->view->assignMultiple([
            'intents' => $intents,
        ]);
    }

    public function addAction()
    {
        if ($this->request->hasArgument('trainingDataText') && !empty($this->request->getArgument('trainingDataText')) && $this->request->hasArgument('trainingDataIntent') && !empty($this->request->getArgument('trainingDataIntent'))) {
            $trainingDataText = $this->request->getArgument('trainingDataText');
            $trainingDataIntent = $this->request->getArgument('trainingDataIntent');

            $rasaUrl = $this->extensionConfiguration->get('chatbot_rasa', 'rasaUrl');
            $this->rasaApiUtility = new RasaApiUtility($rasaUrl);
            $accessToken = $this->authenticate($this->rasaApiUtility);

            $trainingData = new TrainingData();
            $trainingData->setIntent($trainingDataIntent);
            $trainingData->setText($trainingDataText);

            $isTrainingDataAdded = $this->rasaApiUtility->addTrainingData($accessToken, $trainingData);

            if ($isTrainingDataAdded) {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_training_data.xlf:training_data_added'),
                    '',
                    AbstractMessage::OK
                );
                $this->redirect('index');
            } else {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_training_data.xlf:training_data_not_added'),
                    '',
                    AbstractMessage::ERROR
                );

                $this->redirect('showAdd');
            }

        } else {
            $this->addFlashMessage(
                LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_training_data.xlf:training_data_not_valid'),
                '',
                AbstractMessage::ERROR
            );

            $this->redirect('showAdd');
        }
    }

}
