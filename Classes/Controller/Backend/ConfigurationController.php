<?php

namespace SvenLie\Chatbots\Controller\Backend;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class ConfigurationController extends Controller
{
    protected string $controllerName = 'Backend\Configuration';

    protected string $moduleName = 'chatbotChatbot_ChatbotRasaConfiguration';

    /**
     * @var ExtensionConfiguration
     */
    protected ExtensionConfiguration $extensionConfiguration;

    public function __construct(ExtensionConfiguration $extensionConfiguration)
    {
        parent::__construct($extensionConfiguration);
        $this->extensionConfiguration = $extensionConfiguration;
    }

    public function indexAction()
    {
        $this->view->assign('rasaUrl', $this->extensionConfiguration->get('chatbots', 'rasaUrl'));
        $this->view->assign('rasaUsername', $this->extensionConfiguration->get('chatbots', 'rasaUsername'));
        $this->view->assign('rasaPassword', $this->extensionConfiguration->get('chatbots', 'rasaPassword'));
    }

    /**
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function saveAction(): void
    {

        $rasaUrl = $this->request->getArgument('rasaUrl');
        $rasaUsername = $this->request->getArgument('rasaUsername');
        $rasaPassword = $this->request->getArgument('rasaPassword');

        $this->extensionConfiguration->set('chatbots', ['rasaUrl' => $rasaUrl, 'rasaUsername' => $rasaUsername, 'rasaPassword' => $rasaPassword]);


        $this->addFlashMessage(
            LocalizationUtility::translate('LLL:EXT:chatbots/Resources/Private/Language/Backend/locallang_mod.xlf:submit_success'),
            '',
            AbstractMessage::OK
        );
        $this->redirect('index');
    }

}
