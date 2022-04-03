<?php

namespace SvenLie\Chatbots\Controller\Backend;

use SvenLie\Chatbots\Domain\Model\User;
use SvenLie\Chatbots\Utility\RasaApiUtility;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class Controller extends ActionController
{

    protected ExtensionConfiguration $extensionConfiguration;

    /*
     * need to set in sub-controllers
     */
    protected string $controllerName = '';

    /*
     * need to set in sub-controllers
     */
    protected string $moduleName = '';

    protected IconFactory $iconFactory;

    /**
     * BackendTemplateContainer
     *
     * @var BackendTemplateView
     */
    protected $view;

    /**
     * Backend Template Container
     *
     * @var BackendTemplateView
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /**
     * @var string
     */
    protected string $accessToken;

    public function __construct(ExtensionConfiguration $extensionConfiguration)
    {
        $this->extensionConfiguration = $extensionConfiguration;
        $this->iconFactory = GeneralUtility::makeInstance(IconFactory::class);
    }

    protected function initializeView(ViewInterface $view)
    {
        parent::initializeView($view);
        $this->addCssToBackendView();
    }

    protected function authenticate(RasaApiUtility $rasaApiUtility)
    {
        $rasaUsername = $this->extensionConfiguration->get('chatbots', 'rasaUsername');
        $rasaPassword = $this->extensionConfiguration->get('chatbots', 'rasaPassword');

        $user = new User();
        $user->setUsername($rasaUsername);
        $user->setPassword($rasaPassword);

        return $rasaApiUtility->authenticateWithUser($user);
    }

    protected function createAddAndRefreshButtons()
    {

        $addButtonParameter = [
            'tx_chatbotrasa_' . strtolower($this->moduleName) => [
                'action' => 'showAdd',
                'controller' => $this->controllerName
            ]
        ];

        $refreshButtonParameter = [
            'tx_chatbotrasa_' . strtolower($this->moduleName) => [
                'action' => 'index',
                'controller' => $this->controllerName
            ]
        ];

        $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();
        $uriBuilder = $this->objectManager->get(UriBuilder::class);
        $uriBuilder->setRequest($this->request);
        $buttons = [];

        $uriAdd = $this->uriBuilder->setArguments($addButtonParameter)->buildBackendUri();
        $uriRefresh = $this->uriBuilder->setArguments($refreshButtonParameter)->buildBackendUri();
        $buttons[] = $buttonBar->makeLinkButton()
            ->setHref($uriAdd)
            ->setTitle("Add record")
            ->setIcon($this->iconFactory->getIcon('actions-add', Icon::SIZE_SMALL));

        $buttons[] = $buttonBar->makeLinkButton()
            ->setHref($uriRefresh)
            ->setTitle("Update records")
            ->setIcon($this->iconFactory->getIcon('actions-refresh', Icon::SIZE_SMALL));

        foreach ($buttons as $button) {
            $buttonBar->addButton($button, ButtonBar::BUTTON_POSITION_LEFT);
        }

    }

    protected function createBackButton()
    {
        $backButtonParameter = [
            'tx_chatbotrasa_' . strtolower($this->moduleName) => [
                'action' => 'index',
                'controller' => $this->controllerName
            ]
        ];

        $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();
        $uriBuilder = $this->objectManager->get(UriBuilder::class);
        $uriBuilder->setRequest($this->request);

        $uri = $this->uriBuilder->setArguments($backButtonParameter)->buildBackendUri();

        $button = $buttonBar->makeLinkButton()
            ->setHref($uri)
            ->setTitle("Go back")
            ->setIcon($this->iconFactory->getIcon('actions-arrow-down-left', Icon::SIZE_SMALL));

        $buttonBar->addButton($button, ButtonBar::BUTTON_POSITION_LEFT);

    }

    protected function addCssToBackendView()
    {
        $this->view->getModuleTemplate()->getPageRenderer()->addCssFile('EXT:chatbots/Resources/Public/Css/Backend/Table.css');
        $this->view->getModuleTemplate()->getPageRenderer()->addCssFile('EXT:chatbots/Resources/Public/Css/Backend/AceEditor.css');
    }

    /*
     * Save Button when using yaml editor
     */
    protected function createSaveButton($actionName)
    {
        $saveButtonParameter = [
            'tx_chatbotrasa_' . strtolower($this->moduleName) => [
                'action' => $actionName,
                'controller' => $this->controllerName
            ]
        ];

        $this->view->getModuleTemplate()->getPageRenderer()->addInlineLanguageLabelArray([
            'not_saved' => LocalizationUtility::translate('LLL:EXT:chatbots/Resources/Private/Language/Backend/locallang_mod.xlf:not_saved'),
            'saved' => LocalizationUtility::translate('LLL:EXT:chatbots/Resources/Private/Language/Backend/locallang_mod.xlf:saved')
        ]);

        $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();
        $uriBuilder = $this->objectManager->get(UriBuilder::class);
        $uriBuilder->setRequest($this->request);

        $uri = $this->uriBuilder->setArguments($saveButtonParameter)->buildBackendUri();

        $button = $buttonBar->makeLinkButton()
            ->setHref($uri)
            ->setTitle("Save")
            ->setClasses('editor-save')
            ->setIcon($this->iconFactory->getIcon('actions-save', Icon::SIZE_SMALL));

        $buttonBar->addButton($button, ButtonBar::BUTTON_POSITION_LEFT);
    }
}
