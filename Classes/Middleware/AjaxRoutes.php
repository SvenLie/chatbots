<?php

namespace SvenLie\ChatbotRasa\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SvenLie\ChatbotRasa\Domain\Model\ChatSession;
use SvenLie\ChatbotRasa\Domain\Model\User;
use SvenLie\ChatbotRasa\Domain\Repository\ChatSessionRepository;
use SvenLie\ChatbotRasa\Utility\ExtensionConfigurationUtility;
use SvenLie\ChatbotRasa\Utility\RasaApiUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class AjaxRoutes implements MiddlewareInterface
{
    /**
     * @var RasaApiUtility
     */
    protected RasaApiUtility $rasaApiUtility;

    protected ExtensionConfigurationUtility $extensionConfigurationUtility;

    protected ExtensionConfiguration $extensionConfiguration;

    /**
     * @var string
     */
    protected string $requestPath = '';

    /**
     * @var string
     */
    private string $requestMethod;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->extensionConfigurationUtility = GeneralUtility::makeInstance(ExtensionConfigurationUtility::class);
        $this->extensionConfiguration = $this->extensionConfigurationUtility->getExtensionConfiguration();

        if ($this->extensionConfigurationUtility->isExtensionConfigurationValid()) {
            $rasaUrl = $this->extensionConfiguration->get('chatbot_rasa', 'rasaUrl');
            $this->rasaApiUtility = new RasaApiUtility($rasaUrl);
        } else {
            return $handler->handle($request);
        }

        $this->requestPath = $request->getUri()->getPath();
        $this->requestMethod = $request->getMethod();
        if ($this->isAjaxRoute()) {
            return $this->handleAjaxRouting($request, $handler);
        }
        return $handler->handle($request);
    }

    protected function isAjaxRoute()
    {
        return strpos($this->requestPath, '/ajax/') !== false;
    }

    protected function handleAjaxRouting(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->isRequestMatching('/ajax/chatbot/start-conversation', 'POST')) {
            return $this->startChatbotConversation($request);
        }
        if ($this->isRequestMatching('/ajax/chatbot/end-conversation', 'POST')) {
            return $this->endChatbotConversation($request);
        }
        if ($this->isRequestMatching('/ajax/chatbot/chat', 'POST')) {
            return $this->chat($request);
        }
        return $handler->handle($request);
    }

    protected function isRequestMatching(string $route, string $method)
    {
        return strpos($this->requestPath, $route) !== false && $this->requestMethod === $method;
    }

    /**
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    protected function startChatbotConversation(ServerRequestInterface $request)
    {
        $accessToken = $this->authenticateUser();
        $response = GeneralUtility::makeInstance(Response::class);
        $response = $response->withHeader('Content-Type', 'application/json');

        if ($accessToken) {
            $chatTokenResponse = $this->rasaApiUtility->getChatToken($accessToken);

            if (!empty($chatTokenResponse['chat_token'])) {
                $chatToken = $chatTokenResponse['chat_token'];

                $jwtAccessTokenResponse = $this->rasaApiUtility->authenticateWithChatToken($chatToken);

                if (!empty($jwtAccessTokenResponse['access_token'])) {

                    $jwtAccessToken = $jwtAccessTokenResponse['access_token'];
                    $senderToken = $jwtAccessTokenResponse['conversation_id'];

                    $chatSession = GeneralUtility::makeInstance(ChatSession::class);
                    $chatSession->setSenderToken($senderToken);
                    $chatSession->setAccessToken($jwtAccessToken);
                    $chatSession->setTimestamp(time());

                    /*
                     * Change for T3 v11
                     */
                    /** @var ObjectManager $objectManager */
                    $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
                    /** @var ChatSessionRepository $chatSessionRepository */
                    $chatSessionRepository = $objectManager->get(ChatSessionRepository::class);
                    /** @var PersistenceManager $persistenceManager */
                    $persistenceManager = $objectManager->get(PersistenceManager::class);

                    $chatSessionRepository->add($chatSession);
                    $persistenceManager->persistAll();

                    $message = json_encode(['sender_token' => $senderToken]);
                    $status = 200;

                } else {
                    $message = json_encode(['error' => "Getting jwt access token failed"]);
                    $status = 500;
                }

            } else {
                $message = json_encode(['error' => "Getting chat token failed"]);
                $status = 500;
            }
        } else {
            $message = json_encode(['error' => "Authentication with credentials failed"]);
            $status = 500;
        }

        $response->getBody()->write($message);

        return $response->withStatus($status);
    }

    /**
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    protected function endChatbotConversation(ServerRequestInterface $request)
    {
        $response = GeneralUtility::makeInstance(Response::class);
        $response = $response->withHeader('Content-Type', 'application/json');

        $content = json_decode($request->getBody()->getContents());
        $senderToken = $content->sender_token;

        if (!empty($senderToken)) {
            /*
            * Change for T3 v11
            */
            /** @var ObjectManager $objectManager */
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            /** @var ChatSessionRepository $chatSessionRepository */
            $chatSessionRepository = $objectManager->get(ChatSessionRepository::class);
            /** @var PersistenceManager $persistenceManager */
            $persistenceManager = $objectManager->get(PersistenceManager::class);

            $chatSession = $chatSessionRepository->findBySenderToken($senderToken);

            if (!empty($chatSession)) {
                $chatSessionRepository->remove($chatSession);
                $persistenceManager->persistAll();

                $status = 204;
            } else {
                $message = json_encode(['error' => "No session found"]);
                $response->getBody()->write($message);
                $status = 500;
            }
        } else {
            $message = json_encode(['error' => "Invalid request"]);
            $response->getBody()->write($message);
            $status = 500;
        }

        return $response->withStatus($status);
    }

    protected function chat(ServerRequestInterface $request)
    {
        $response = GeneralUtility::makeInstance(Response::class);
        $response = $response->withHeader('Content-Type', 'application/json');

        $content = json_decode($request->getBody()->getContents());
        $senderToken = $content->sender_token;
        $message = $content->message;

        if (!empty($senderToken) && !(empty($message))) {
            /*
            * Change for T3 v11
            */
            /** @var ObjectManager $objectManager */
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            /** @var ChatSessionRepository $chatSessionRepository */
            $chatSessionRepository = $objectManager->get(ChatSessionRepository::class);

            /** @var ChatSession $chatSession */
            $chatSession = $chatSessionRepository->findBySenderToken($senderToken);

            if (!empty($chatSession)) {
                $accessToken = $chatSession->getAccessToken();
                $chatResponse = $this->rasaApiUtility->chat($message, $accessToken);

                if (!empty($chatResponse)) {
                    $response->getBody()->write(json_encode($chatResponse));
                } else {
                    $message = json_encode([['text' => LocalizationUtility::translate("LLL:EXT:chatbot_rasa/Resources/Private/Language/locallang.xlf:no-content")]]);
                    $response->getBody()->write($message);
                }
                $status = 200;

            } else {
                $message = json_encode(['error' => "No session found"]);
                $response->getBody()->write($message);
                $status = 500;
            }


        } else {
            $message = json_encode(['error' => "Invalid request"]);
            $response->getBody()->write($message);
            $status = 500;
        }

        return $response->withStatus($status);
    }

    protected function authenticateUser()
    {
        $rasaUsername = $this->extensionConfiguration->get('chatbot_rasa', 'rasaUsername');
        $rasaPassword = $this->extensionConfiguration->get('chatbot_rasa', 'rasaPassword');

        $user = new User();
        $user->setUsername($rasaUsername);
        $user->setPassword($rasaPassword);

        return $this->rasaApiUtility->authenticateWithUser($user);

    }
}
