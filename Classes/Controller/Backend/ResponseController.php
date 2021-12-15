<?php

namespace SvenLie\ChatbotRasa\Controller\Backend;

use SvenLie\ChatbotRasa\Domain\Model\Response;
use SvenLie\ChatbotRasa\Utility\ExtensionConfigurationUtility;
use SvenLie\ChatbotRasa\Utility\RasaApiUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class ResponseController extends Controller
{
    protected string $controllerName = 'Backend\Response';

    protected string $moduleName = 'chatbotRasaChatbot_ChatbotRasaResponses';

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
                $responses = $this->rasaApiUtility->getResponses($accessToken);

                $this->view->assign('responses', $responses);
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

    public function showUpdateAction()
    {
        if ($this->request->hasArgument('responseId') && $this->request->hasArgument('responseText') && $this->request->hasArgument('responseName')) {
            $this->createBackButton();
            $responseId = $this->request->getArgument('responseId');
            $responseText = $this->request->getArgument('responseText');
            $responseName = $this->request->getArgument('responseName');

            $rasaUrl = $this->extensionConfiguration->get('chatbot_rasa', 'rasaUrl');
            $this->rasaApiUtility = new RasaApiUtility($rasaUrl);
            $accessToken = $this->authenticate($this->rasaApiUtility);

            $responseGroups = $this->rasaApiUtility->getResponseGroups($accessToken);

            $this->view->assignMultiple([
                'responseGroups' => $responseGroups,
                'responseId' => $responseId,
                'responseText' => $responseText,
                'responseName' => $responseName
            ]);

        } else {
            $this->addFlashMessage(
                LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_response.xlf:response_not_valid'),
                '',
                AbstractMessage::ERROR
            );

            $this->redirect('index');
        }
    }

    public function updateAction()
    {
        if ($this->request->hasArgument('responseId') && !empty($this->request->getArgument('responseId')) && $this->request->hasArgument('responseText') && !empty($this->request->getArgument('responseText')) && $this->request->hasArgument('responseName') && !empty($this->request->getArgument('responseName'))) {
            $responseId = $this->request->getArgument('responseId');
            $responseText = $this->request->getArgument('responseText');
            $responseName = $this->request->getArgument('responseName');

            if (!preg_match("/utter_\w+/", $responseName)) {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_response.xlf:response_name_invalid'),
                    '',
                    AbstractMessage::ERROR
                );

                $this->redirect('index');
            }

            $rasaUrl = $this->extensionConfiguration->get('chatbot_rasa', 'rasaUrl');
            $this->rasaApiUtility = new RasaApiUtility($rasaUrl);
            $accessToken = $this->authenticate($this->rasaApiUtility);

            $response = new Response();
            $response->setId($responseId);
            $response->setResponseName($responseName);
            $response->setText($responseText);

            $isResponseUpdated = $this->rasaApiUtility->updateResponse($accessToken, $response);

            if ($isResponseUpdated) {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_response.xlf:response_updated'),
                    '',
                    AbstractMessage::OK
                );
                $this->redirect('index');
            } else {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_response.xlf:response_not_updated'),
                    '',
                    AbstractMessage::ERROR
                );

                $this->redirect('showUpdate', 'Backend\Response', 'ChatbotRasa', ['responseId' => $responseId, 'responseName' => $responseName, 'responseText' => $responseText]);
            }

        } else {
            $this->addFlashMessage(
                LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_response.xlf:response_not_valid'),
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

        $responseGroups = $this->rasaApiUtility->getResponseGroups($accessToken);

        $this->view->assignMultiple([
            'responseGroups' => $responseGroups,
        ]);
    }

    public function addAction()
    {
        if ($this->request->hasArgument('responseText') && !empty($this->request->getArgument('responseText')) && $this->request->hasArgument('responseName') && !empty($this->request->getArgument('responseName'))) {
            $responseText = $this->request->getArgument('responseText');
            $responseName = $this->request->getArgument('responseName');

            if (!preg_match("/utter_\w+/", $responseName)) {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_response.xlf:response_name_invalid'),
                    '',
                    AbstractMessage::ERROR
                );

                $this->redirect('showAdd');
            }

            $rasaUrl = $this->extensionConfiguration->get('chatbot_rasa', 'rasaUrl');
            $this->rasaApiUtility = new RasaApiUtility($rasaUrl);
            $accessToken = $this->authenticate($this->rasaApiUtility);

            $response = new Response();
            $response->setResponseName($responseName);
            $response->setText($responseText);

            $isResponseAdded = $this->rasaApiUtility->addResponse($accessToken, $response);

            if ($isResponseAdded) {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_response.xlf:response_added'),
                    '',
                    AbstractMessage::OK
                );
                $this->redirect('index');
            } else {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_response.xlf:response_not_added'),
                    '',
                    AbstractMessage::ERROR
                );

                $this->redirect('showAdd');
            }

        } else {
            $this->addFlashMessage(
                LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_response.xlf:response_not_valid'),
                '',
                AbstractMessage::ERROR
            );

            $this->redirect('showAdd');
        }
    }

    public function deleteAction()
    {
        if ($this->request->hasArgument('responseId')) {
            $responseId = $this->request->getArgument('responseId');
            $rasaUrl = $this->extensionConfiguration->get('chatbot_rasa', 'rasaUrl');
            $this->rasaApiUtility = new RasaApiUtility($rasaUrl);

            $accessToken = $this->authenticate($this->rasaApiUtility);

            $isModelDeleted = $this->rasaApiUtility->deleteResponse($accessToken, $responseId);

            if ($isModelDeleted) {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_response.xlf:response_deleted'),
                    '',
                    AbstractMessage::OK
                );
            } else {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_response.xlf:response_not_deleted'),
                    '',
                    AbstractMessage::ERROR
                );
            }


        } else {
            $this->addFlashMessage(
                LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_response.xlf:response_not_deleted'),
                '',
                AbstractMessage::ERROR
            );
        }
        $this->redirect('index');
    }

}
