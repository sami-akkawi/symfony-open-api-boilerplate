<?php declare(strict_types=1);

namespace App\Message;

final class Message
{
    const ID = 'id';
    const TYPE = 'type';
    const TRANSLATION_KEY = 'translationKey';
    const DEFAULT_TEXT = 'defaultText';
    const PLACEHOLDERS = 'placeholders';

    private MessageId $id;
    private MessageType $type;
    private MessageTranslationKey $translationKey;
    private MessageDefaultText $defaultText;
    private MessagePlaceholders $placeholders;

    private function __construct(
        MessageId $id,
        MessageType $type,
        MessageTranslationKey $translationKey,
        MessageDefaultText $defaultText,
        MessagePlaceholders $placeholders
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->translationKey = $translationKey;
        $this->defaultText = $defaultText;
        $this->placeholders = $placeholders;
    }

    public static function generateInfo(string $translationKey, ?string $defaultText = null, array $placeholders = []): self
    {
        return new self(
            MessageId::generate(),
            MessageType::generateInfo(),
            MessageTranslationKey::fromString($translationKey),
            MessageDefaultText::fromString($defaultText ?? $translationKey),
            MessagePlaceholders::fromArray($placeholders)
        );
    }

    public static function generateError(string $translationKey, ?string $defaultText = null, array $placeholders = []): self
    {
        return new self(
            MessageId::generate(),
            MessageType::generateError(),
            MessageTranslationKey::fromString($translationKey),
            MessageDefaultText::fromString($defaultText ?? $translationKey),
            MessagePlaceholders::fromArray($placeholders)
        );
    }

    public static function generateWarning(string $translationKey, ?string $defaultText = null, array $placeholders = []): self
    {
        return new self(
            MessageId::generate(),
            MessageType::generateWarning(),
            MessageTranslationKey::fromString($translationKey),
            MessageDefaultText::fromString($defaultText ?? $translationKey),
            MessagePlaceholders::fromArray($placeholders)
        );
    }

    public static function generateSuccess(string $translationKey, ?string $defaultText = null, array $placeholders = []): self
    {
        return new self(
            MessageId::generate(),
            MessageType::generateSuccess(),
            MessageTranslationKey::fromString($translationKey),
            MessageDefaultText::fromString($defaultText ?? $translationKey),
            MessagePlaceholders::fromArray($placeholders)
        );
    }

    public function toArray(): array
    {
        return [
            self::ID => $this->id->toString(),
            self::TYPE => $this->type->toString(),
            self::TRANSLATION_KEY => $this->translationKey->toString(),
            self::DEFAULT_TEXT => $this->defaultText->toString(),
            self::PLACEHOLDERS => $this->placeholders->toArray()
        ];
    }

    public function getDefaultText(): MessageDefaultText
    {
        return $this->defaultText;
    }

    public static function fromArray(array $data): self
    {
        if (!in_array(self::ID, $data)) {
            throw new \LogicException(self::ID . ' is not defined in $data');
        }
        if (!in_array(self::TYPE, $data)) {
            throw new \LogicException(self::TYPE . ' is not defined in $data');
        }
        if (!in_array(self::TRANSLATION_KEY, $data)) {
            throw new \LogicException(self::TYPE . ' is not defined in $data');
        }
        return new self(
            MessageId::fromString($data[self::ID]),
            MessageType::fromString($data[self::TYPE]),
            MessageTranslationKey::fromString($data[self::TRANSLATION_KEY]),
            MessageDefaultText::fromString($data[self::DEFAULT_TEXT] ?? $data[self::TRANSLATION_KEY]),
            MessagePlaceholders::fromArray($data[self::PLACEHOLDERS] ?? []),
        );
    }
}