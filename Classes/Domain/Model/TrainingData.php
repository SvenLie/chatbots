<?php

namespace SvenLie\ChatbotRasa\Domain\Model;

class TrainingData
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
    protected string $intent;

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
    public function getIntent(): string
    {
        return $this->intent;
    }

    /**
     * @param string $intent
     */
    public function setIntent(string $intent): void
    {
        $this->intent = $intent;
    }

    public function jsonSerialize()
    {
        return json_encode([
            'id' => $this->id,
            'text' => $this->text,
            'intent' => $this->intent
        ]);
    }

    public function jsonSerializeNewObject()
    {
        return json_encode([
            'text' => $this->text,
            'intent' => $this->intent
        ]);
    }

}
