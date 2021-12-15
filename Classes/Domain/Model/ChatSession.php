<?php

namespace SvenLie\ChatbotRasa\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class ChatSession extends AbstractEntity
{
    /**
     * @var string
     */
    protected string $senderToken = '';

    /**
     * @var string
     */
    protected string $accessToken = '';

    /**
     * @var string
     */
    protected string $timestamp = '';

    /**
     * @return string
     */
    public function getSenderToken(): string
    {
        return $this->senderToken;
    }

    /**
     * @param string $senderToken
     */
    public function setSenderToken(string $senderToken): void
    {
        $this->senderToken = $senderToken;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return string
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * @param string $timestamp
     */
    public function setTimestamp(string $timestamp): void
    {
        $this->timestamp = $timestamp;
    }
}
