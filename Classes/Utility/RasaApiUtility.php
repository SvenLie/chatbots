<?php

namespace SvenLie\ChatbotRasa\Utility;

use GuzzleHttp\Exception\RequestException;
use SvenLie\ChatbotRasa\Domain\Model\TrainingData;
use SvenLie\ChatbotRasa\Domain\Model\User;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RasaApiUtility
{
    /**
     * @var RequestFactory
     */
    protected RequestFactory $requestFactory;

    /**
     * @var string
     */
    protected string $instanceUrl;

    public function __construct($instanceUrl)
    {
        $this->requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
        $this->instanceUrl = $instanceUrl;
    }

    /**
     * @param User $user
     * @return false
     */
    public function authenticateWithUser(User $user)
    {
        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/auth";
        } else {
            $uri = $this->instanceUrl . "/api/auth";
        }

        try {
            $additionalOptions = [
                'body' => $user->jsonSerialize(),
            ];

            /** @var Response $response */
            $response = $this->requestFactory->request($uri, "POST", $additionalOptions);
        } catch (RequestException $e) {
            return false;
        }

        if ($response->getStatusCode() === 200) {
            $content = json_decode($response->getBody()->getContents());
            return $content->access_token;
        } else {
            return false;
        }
    }

    public function authenticateWithChatToken(string $chatToken)
    {
        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/auth/jwt";
        } else {
            $uri = $this->instanceUrl . "/api/auth/jwt";
        }

        try {
            $additionalOptions = [
                'body' => json_encode(['chat_token' => $chatToken]),
            ];

            /** @var Response $response */
            $response = $this->requestFactory->request($uri, "POST", $additionalOptions);
        } catch (RequestException $e) {
            return false;
        }

        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents(), true);
        } else {
            return false;
        }
    }

    public function chat(string $message, string $accessToken)
    {
        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "webhooks/rasa/webhook";
        } else {
            $uri = $this->instanceUrl . "/webhooks/rasa/webhook";
        }

        try {
            $additionalOptions = [
                'headers' => ['Authorization' => 'Bearer ' . $accessToken],
                'body' => json_encode(['message' => $message])
            ];

            /** @var Response $response */
            $response = $this->requestFactory->request($uri, "POST", $additionalOptions);

        } catch (RequestException $exception) {
            return false;
        }

        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents());
        } else {
            DebugUtility::debug($response->getBody()->getContents());
            return false;
        }
    }

    public function getChatToken($accessToken)
    {
        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/chatToken";
        } else {
            $uri = $this->instanceUrl . "/api/chatToken";
        }

        try {
            $additionalOptions = [
                'headers' => ['Authorization' => 'Bearer ' . $accessToken],
                'body' => json_encode(["bot_name" => 'Chatbot'])
            ];

            /** @var Response $response */
            $response = $this->requestFactory->request($uri, "PUT", $additionalOptions);

        } catch (RequestException $exception) {
            return false;
        }

        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents());
        } else {
            return false;
        }

        //return $this->doGetRequestAndReturnContent($accessToken, $uri);
    }

    public function getHealthStatus($accessToken)
    {
        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/health";
        } else {
            $uri = $this->instanceUrl . "/api/health";
        }

        return $this->doGetRequestAndReturnContent($accessToken, $uri);
    }

    public function markModelAsActive($accessToken, $modelName): bool
    {
        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/projects/default/models/" . $modelName . "/tags/production";
        } else {
            $uri = $this->instanceUrl . "/api/projects/default/models/" . $modelName . "/tags/production";
        }

        try {
            $additionalOptions = [
                'headers' => ['Authorization' => 'Bearer ' . $accessToken]
            ];

            /** @var Response $response */
            $response = $this->requestFactory->request($uri, "PUT", $additionalOptions);

        } catch (RequestException $exception) {
            return false;
        }

        if ($response->getStatusCode() === 204) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteModel($accessToken, $modelName): bool
    {
        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/projects/default/models/" . $modelName;
        } else {
            $uri = $this->instanceUrl . "/api/projects/default/models/" . $modelName;
        }

        return $this->doDeleteRequest($accessToken, $uri);
    }

    public function getTrainedModels($accessToken)
    {
        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/projects/default/models";
        } else {
            $uri = $this->instanceUrl . "/api/projects/default/models";
        }

        return $this->doGetRequestAndReturnContent($accessToken, $uri);
    }

    public function trainModel($accessToken): bool
    {
        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/projects/default/models/jobs";
        } else {
            $uri = $this->instanceUrl . "/api/projects/default/models/jobs";
        }

        try {
            $additionalOptions = [
                'headers' => ['Authorization' => 'Bearer ' . $accessToken]
            ];

            /** @var Response $response */
            $response = $this->requestFactory->request($uri, "POST", $additionalOptions);

        } catch (RequestException $exception) {
            return false;
        }


        if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * @param $accessToken
     * @return false|mixed
     */
    public function getTrainingData($accessToken)
    {
        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/projects/default/training_examples";
        } else {
            $uri = $this->instanceUrl . "/api/projects/default/training_examples";
        }

        return $this->doGetRequestAndReturnContent($accessToken, $uri);
    }

    public function deleteTrainingData($accessToken, $trainingDataId): bool
    {
        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/projects/default/training_examples/" . $trainingDataId;
        } else {
            $uri = $this->instanceUrl . "/api/projects/default/training_examples/" . $trainingDataId;
        }

        return $this->doDeleteRequest($accessToken, $uri);

    }

    public function updateTrainingData($accessToken, TrainingData $trainingData): bool
    {
        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/projects/default/training_examples/" . $trainingData->getId();
        } else {
            $uri = $this->instanceUrl . "/api/projects/default/training_examples/" . $trainingData->getId();
        }

        try {
            $additionalOptions = [
                'headers' => ['Authorization' => 'Bearer ' . $accessToken],
                'body' => $trainingData->jsonSerialize(),
            ];

            /** @var Response $response */
            $response = $this->requestFactory->request($uri, "PUT", $additionalOptions);
        } catch (RequestException $e) {
            return false;
        }

        if ($response->getStatusCode() === 200) {
            return true;
        } else {
            return false;
        }
    }

    public function getIntents($accessToken)
    {
        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/projects/default/intents";
        } else {
            $uri = $this->instanceUrl . "/api/projects/default/intents";
        }

        return $this->doGetRequestAndReturnContent($accessToken, $uri);
    }

    /**
     * @param $accessToken
     * @param TrainingData $trainingData
     * @return bool
     */
    public function addTrainingData($accessToken, $trainingData)
    {

        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/projects/default/training_examples";
        } else {
            $uri = $this->instanceUrl . "/api/projects/default/training_examples";
        }

        try {
            $additionalOptions = [
                'headers' => ['Authorization' => 'Bearer ' . $accessToken],
                'body' => $trainingData->jsonSerializeNewObject(),
            ];

            /** @var Response $response */
            $response = $this->requestFactory->request($uri, "POST", $additionalOptions);
        } catch (RequestException $e) {
            return false;
        }

        if ($response->getStatusCode() === 200) {
            return true;
        } else {
            return false;
        }
    }

    public function getRules($accessToken)
    {
        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/rules";
        } else {
            $uri = $this->instanceUrl . "/api/rules";
        }

        return $this->doGetRequestAndReturnContent($accessToken, $uri);
    }

    public function updateRule($accessToken, $ruleId, $ruleContent): bool
    {
        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/rules/" . $ruleId;
        } else {
            $uri = $this->instanceUrl . "/api/rules/" . $ruleId;
        }

        return $this->doUpdateRequestWithYamlContent($accessToken, $ruleContent, $uri);
    }

    public function addRule($accessToken, $ruleContent): bool
    {
        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/rules";
        } else {
            $uri = $this->instanceUrl . "/api/rules";
        }

        return $this->doAddRequestWithYamlContent($accessToken, $ruleContent, $uri);
    }

    public function deleteRule($accessToken, $ruleId): bool
    {
        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/rules/" . $ruleId;
        } else {
            $uri = $this->instanceUrl . "/api/rules/" . $ruleId;
        }

        return $this->doDeleteRequest($accessToken, $uri);
    }

    public function getStories($accessToken)
    {
        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/stories";
        } else {
            $uri = $this->instanceUrl . "/api/stories";
        }

        return $this->doGetRequestAndReturnContent($accessToken, $uri);
    }

    public function addStory($accessToken, $storyContent): bool
    {
        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/stories";
        } else {
            $uri = $this->instanceUrl . "/api/stories";
        }

        return $this->doAddRequestWithYamlContent($accessToken, $storyContent, $uri);
    }

    public function updateStory($accessToken, $storyId, $storyContent): bool
    {
        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/stories/" . $storyId;
        } else {
            $uri = $this->instanceUrl . "/api/stories/" . $storyId;
        }

        return $this->doUpdateRequestWithYamlContent($accessToken, $storyContent, $uri);
    }

    public function deleteStory($accessToken, $storyId): bool
    {
        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/stories/" . $storyId;
        } else {
            $uri = $this->instanceUrl . "/api/stories/" . $storyId;
        }

        return $this->doDeleteRequest($accessToken, $uri);
    }

    public function getResponses($accessToken)
    {
        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/responses";
        } else {
            $uri = $this->instanceUrl . "/api/responses";
        }

        return $this->doGetRequestAndReturnContent($accessToken, $uri);
    }

    public function getResponseGroups($accessToken)
    {
        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/responseGroups";
        } else {
            $uri = $this->instanceUrl . "/api/responseGroups";
        }

        return $this->doGetRequestAndReturnContent($accessToken, $uri);
    }

    public function updateResponse($accessToken, \SvenLie\ChatbotRasa\Domain\Model\Response $response): bool
    {
        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/responses/" . $response->getId();
        } else {
            $uri = $this->instanceUrl . "/api/responses/" . $response->getId();
        }

        try {
            $additionalOptions = [
                'headers' => ['Authorization' => 'Bearer ' . $accessToken],
                'body' => $response->jsonSerialize(),
            ];

            /** @var Response $response */
            $response = $this->requestFactory->request($uri, "PUT", $additionalOptions);
        } catch (RequestException $e) {
            return false;
        }

        if ($response->getStatusCode() === 200) {
            return true;
        } else {
            return false;
        }
    }

    public function addResponse($accessToken, \SvenLie\ChatbotRasa\Domain\Model\Response $response): bool
    {

        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/responses";
        } else {
            $uri = $this->instanceUrl . "/api/responses";
        }

        try {
            $additionalOptions = [
                'headers' => ['Authorization' => 'Bearer ' . $accessToken],
                'body' => $response->jsonSerializeNewObject(),
            ];

            /** @var Response $response */
            $response = $this->requestFactory->request($uri, "POST", $additionalOptions);
        } catch (RequestException $e) {
            return false;
        }

        if ($response->getStatusCode() === 201) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteResponse($accessToken, $responseId): bool
    {
        if ($this->hasInstanceHasTrailingSlash()) {
            $uri = $this->instanceUrl . "api/responses/" . $responseId;
        } else {
            $uri = $this->instanceUrl . "/api/responses/" . $responseId;
        }

        return $this->doDeleteRequest($accessToken, $uri);

    }

    /**
     * @return bool
     */
    protected function hasInstanceHasTrailingSlash(): bool
    {
        $positionOfLastSlash = strrpos($this->instanceUrl, '/');

        if ($positionOfLastSlash === (strlen($this->instanceUrl) - 1)) {
            return true;
        }

        return false;
    }

    /**
     * @param $accessToken
     * @param string $uri
     * @return false|mixed
     */
    protected function doGetRequestAndReturnContent($accessToken, string $uri)
    {
        try {
            $additionalOptions = [
                'headers' => ['Authorization' => 'Bearer ' . $accessToken]
            ];

            /** @var Response $response */
            $response = $this->requestFactory->request($uri, "GET", $additionalOptions);
        } catch (RequestException $exception) {
            return false;
        }

        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents(), true);
        } else {
            return false;
        }
    }

    /**
     * @param $accessToken
     * @param string $uri
     * @return bool
     */
    protected function doDeleteRequest($accessToken, string $uri): bool
    {
        try {
            $additionalOptions = [
                'headers' => ['Authorization' => 'Bearer ' . $accessToken]
            ];

            /** @var Response $response */
            $response = $this->requestFactory->request($uri, "DELETE", $additionalOptions);

        } catch (RequestException $exception) {
            return false;
        }

        if ($response->getStatusCode() === 204) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $accessToken
     * @param $content
     * @param string $uri
     * @return bool
     */
    protected function doUpdateRequestWithYamlContent($accessToken, $content, string $uri): bool
    {
        try {
            $additionalOptions = [
                'headers' => ['Authorization' => 'Bearer ' . $accessToken, 'Content-Type' => 'application/x-yaml'],
                'body' => $content,
            ];

            /** @var Response $response */
            $response = $this->requestFactory->request($uri, "PUT", $additionalOptions);
        } catch (RequestException $e) {
            return false;
        }

        if ($response->getStatusCode() === 200) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $accessToken
     * @param $storyContent
     * @param string $uri
     * @return bool
     */
    protected function doAddRequestWithYamlContent($accessToken, $content, string $uri): bool
    {
        try {
            $additionalOptions = [
                'headers' => ['Authorization' => 'Bearer ' . $accessToken, 'Content-Type' => 'application/x-yaml'],
                'body' => $content,
            ];

            /** @var Response $response */
            $response = $this->requestFactory->request($uri, "POST", $additionalOptions);
        } catch (RequestException $e) {
            return false;
        }

        if ($response->getStatusCode() === 200) {
            return true;
        } else {
            return false;
        }
    }
}
