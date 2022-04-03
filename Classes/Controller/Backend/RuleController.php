<?php

namespace SvenLie\Chatbots\Controller\Backend;

use SvenLie\Chatbots\Utility\ExtensionConfigurationUtility;
use SvenLie\Chatbots\Utility\RasaApiUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class RuleController extends Controller
{
    protected ExtensionConfigurationUtility $extensionConfigurationUtility;

    protected ExtensionConfiguration $extensionConfiguration;

    protected string $controllerName = 'Backend\Rule';

    protected string $moduleName = 'chatbotRasaChatbot_ChatbotRasaRules';


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
                $contribPath = ExtensionManagementUtility::extPath('chatbots', $contribPath);
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
                LocalizationUtility::translate('LLL:EXT:chatbots/Resources/Private/Language/Backend/locallang_mod.xlf:extension_configuration_not_filled'),
                '',
                AbstractMessage::ERROR
            );
        } else {
            $rasaUrl = $this->extensionConfiguration->get('chatbots', 'rasaUrl');
            $this->rasaApiUtility = new RasaApiUtility($rasaUrl);

            $accessToken = $this->authenticate($this->rasaApiUtility);

            if ($accessToken) {
                $this->createAddAndRefreshButtons();
                $rules = $this->rasaApiUtility->getRules($accessToken);

                $this->view->assign('rules', $rules);
            } else {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbots/Resources/Private/Language/Backend/locallang_mod.xlf:connection_not_working'),
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
        if ($this->request->hasArgument('ruleId') && $this->request->hasArgument('ruleStory')) {
            $ruleId = $this->request->getArgument('ruleId');
            $ruleStory = $this->request->getArgument('ruleStory');

            $this->createBackButton();
            $this->createSaveButton('after');

            $this->view->assignMultiple([
                'ruleId' => $ruleId,
                'ruleStory' => $ruleStory
            ]);
        } else {
            $this->addFlashMessage(
                LocalizationUtility::translate('LLL:EXT:chatbots/Resources/Private/Language/Backend/locallang_mod_rule.xlf:rule_not_valid'),
                '',
                AbstractMessage::ERROR
            );

            $this->redirect('index');
        }
    }

    public function updateAction(): string
    {
        if ($this->request->hasArgument('id') && $this->request->hasArgument('content')) {
            $ruleId = $this->request->getArgument('id');
            $content = $this->request->getArgument('content');

            $rasaUrl = $this->extensionConfiguration->get('chatbots', 'rasaUrl');
            $this->rasaApiUtility = new RasaApiUtility($rasaUrl);
            $accessToken = $this->authenticate($this->rasaApiUtility);

            $ruleIsUpdated = $this->rasaApiUtility->updateRule($accessToken, $ruleId, $content);

            if ($ruleIsUpdated) {
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

            $rasaUrl = $this->extensionConfiguration->get('chatbots', 'rasaUrl');
            $this->rasaApiUtility = new RasaApiUtility($rasaUrl);
            $accessToken = $this->authenticate($this->rasaApiUtility);

            $ruleIsAdded = $this->rasaApiUtility->addRule($accessToken, $content);

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
        if ($this->request->hasArgument('ruleId')) {
            $ruleId = $this->request->getArgument('ruleId');
            $rasaUrl = $this->extensionConfiguration->get('chatbots', 'rasaUrl');
            $this->rasaApiUtility = new RasaApiUtility($rasaUrl);

            $accessToken = $this->authenticate($this->rasaApiUtility);

            $isModelDeleted = $this->rasaApiUtility->deleteRule($accessToken, $ruleId);

            if ($isModelDeleted) {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbots/Resources/Private/Language/Backend/locallang_mod_rule.xlf:rule_deleted'),
                    '',
                    AbstractMessage::OK
                );
            } else {
                $this->addFlashMessage(
                    LocalizationUtility::translate('LLL:EXT:chatbots/Resources/Private/Language/Backend/locallang_mod_rule.xlf:rule_not_deleted'),
                    '',
                    AbstractMessage::ERROR
                );
            }


        } else {
            $this->addFlashMessage(
                LocalizationUtility::translate('LLL:EXT:chatbots/Resources/Private/Language/Backend/locallang_mod_rule.xlf:rule_not_deleted'),
                '',
                AbstractMessage::ERROR
            );
        }
        $this->redirect('index');
    }

}
