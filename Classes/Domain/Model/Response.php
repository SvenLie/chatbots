<?php

namespace SvenLie\Chatbots\Domain\Model;

class Response
{
    /**
     * @var string
     */
    protected string $id;

    /**
     * @var string
     */
    protected string $text;

    /**
     * @var string
     */
    protected string $responseName;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getResponseName(): string
    {
        return $this->responseName;
    }

    /**
     * @param string $responseName
     */
    public function setResponseName(string $responseName): void
    {
        $this->responseName = $responseName;
    }

    public function jsonSerialize()
    {
        return json_encode([
            'id' => $this->id,
            'text' => $this->text,
            'response_name' => $this->responseName
        ]);
    }

    public function jsonSerializeNewObject()
    {
        return json_encode([
            'text' => $this->text,
            'response_name' => $this->responseName
        ]);
    }
}
