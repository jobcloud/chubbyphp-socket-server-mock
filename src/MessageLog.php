<?php

declare(strict_types=1);

namespace Chubbyphp\SocketServerMock;

final class MessageLog implements MessageLogInterface
{
    /** @var MessageInterface[] */
    private $messages;

    /**
     * @param MessageInterface[] $messages
     */
    public function __construct(array $messages)
    {
        $this->messages = [];
        foreach ($messages as $message) {
            $this->addMessage($message);
        }
    }

    /**
     * @param array<int, array<string, string>> $data
     */
    public static function createFromArray(array $data): self
    {
        $messages = [];

        foreach ($data as $value) {
            $messages[] = Message::createFromArray($value);
        }

        return new self($messages);
    }

    /**
     * @return MessageInterface|null
     */
    public function getNextMessage()
    {
        return array_shift($this->messages);
    }

    private function addMessage(MessageInterface $message): void
    {
        $this->messages[] = $message;
    }
}
