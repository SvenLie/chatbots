<?php

namespace SvenLie\ChatbotRasa\Controller\Backend;

use SvenLie\ChatbotRasa\Utility\ExtensionConfigurationUtility;
use SvenLie\ChatbotRasa\Utility\RasaApiUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class StoryController extends Controller
{
    protected ExtensionConfigurationUtility $extensionConfigurationUtility;

    protected ExtensionConfiguration $extensionConfiguration;

    protected string $controllerName = 'Backend\Story';

    protected string $moduleName = 'chatbotRasaChatbot_ChatbotRasaStories';


    public function __construct(ExtensionConfigurationUtility $extensionConfigurationUtility, ExtensionConfiguration $extensionConfiguration)
    {
        $this->extensionConfigurationUtility = $extensionConfigurationUtility;
        $this->extensionConfiguration = $extensionConfiguration;
        parent::__construct($extensionConfiguration);
    }

    /**
     *
     * @param ViewInterface $view
     * @throws \Exception
     */
    protected function initializeView(ViewInterface $view)
    {
        parent::initializeView($view);
        if ($this->view->getModuleTemplate() !== null) {
            $pageRenderer = $this->view->getModuleTemplate()->getPageRenderer();
            // Require Ace editor
            if ($this->actionMethodName == 'showUpdateAction' || $this->actionMethodName == 'showAddAction') {
                $contribPath = 'Resources/Public/Contrib/';
                $contribPath = ExtensionManagementUtility::extPath('chatbot_rasa', $contribPath);
                $pageRenderer->addRequireJsConfiguration([
                    'shim' => [],
                    'paths' => [
                        'ace' => PathUtility::getAbsoluteWebPath($contribPath) . 'ace/src'
                    ]
                ]);
                $pageRenderer->loadRequireJsModule('TYPO3/CMS/ChatbotRasa/Backend/Modules/AceEditor');
            }
        }
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
                $stories = $this->rasaApiUtility->getStories($accessToken);

                $this->view->assign('stories', $stories);
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
        if ($this->request->hasArgument('storyId') && $this->request->hasArgument('storyStory')) {
            $storyId = $this->request->getArgument('storyId');
            $storyStory = $this->request->getArgument('storyStory');

            $this->createBackButton();
            $this->createSaveButton('after');

            $this->view->assignMultiple([
                'storyId' => $storyId,
                'storyStory' => $storyStory
            ]);
        } else {
            $this->addFlashMessage(
                LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_story.xlf:story_not_valid'),
                '',
                AbstractMessage::ERROR
            );

            $this->redirect('index');
        }
    }

    public function updateAction()
    {
        if ($this->request->hasArgument('id') && $this->request->hasArgument('content')) {
            $storyId = $this->request->getArgument('id');
            $content = $this->request->getArgument('content');

            $rasaUrl = $this->extensionConfiguration->get('chatbot_rasa', 'rasaUrl');
            $this->rasaApiUtility = new RasaApiUtility($rasaUrl);
            $accessToken = $this->authenticate($this->rasaApiUtility);

            $storyIsUpdated = $this->rasaApiUtility->updateStory($accessToken, $storyId, $content);

            if ($storyIsUpdated) {
                return '{"status": "Ok"}';
            } else {
                return '{"status": "Error"}';
            }
        } else {
            return '{"status": "Error"}';
        }
    }

    public function showAddAction()
    {
        $this->createBackButton();
        $this->createSaveButton('after');
    }

    public function addAction()
    {
        if ($this->request->hasArgument('content')) {
            $content = $this->request->getArgument('content');

            $rasaUrl = $this->extensionConfiguration->get('chatbot_rasa', 'rasaUrl');
            $this->rasaApiUtility = new RasaApiUtility($rasaUrl);
            $accessToken = $this->authenticate($this->rasaApiUtility);

            $ruleIsAdded = $this->rasaApiUtility->addStory($accessToken, $content);

            if ($ruleIsAdded) {
                return '{"status": "Ok"}';
            } else {
                return '{"status": "Error"}';
            }

        } else {
            return '{"status": "Error"}';
        }
    }

    /*
     * needed after asynchron updating a rule (via update or add)
     */
    public function afterAction()
    {
        $this->redirect('index');
    }

    public function deleteAction()
    {
        if ($this->request->hasArgument('storyId')) {
            $storyId = $this->request->getArgument('storyId');
            $rasaUrl = $this->extensionConfiguration->get('chatbot_rasa', 'rasaUrl');
            $this->rasaApiUtility = new RasaApiUtility($rasaUrl);

            $accessToken = $this->authenticate($this->rasaApiUtility);

            $isModelDeleted = $this->rasaApiUtility->deleteStory($accessToken, $storyId);

            if ($isModelDeleted) {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_story.xlf:story_deleted'),
                    '',
                    AbstractMessage::OK
                );
            } else {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_story.xlf:story_not_deleted'),
                    '',
                    AbstractMessage::ERROR
                );
            }


        } else {
            $this->addFlashMessage(
                LocalizationUtility::translate('LLL:EXT:chatbot_rasa/Resources/Private/Language/Backend/locallang_mod_story.xlf:story_not_deleted'),
                '',
                AbstractMessage::ERROR
            );
        }
        $this->redirect('index');
    }

}
