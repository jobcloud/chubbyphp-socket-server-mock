<?php

declare(strict_types=1);

namespace Chubbyphp\SocketServerMock;

final class Message implements MessageInterface
{
    /** @var string */
    private $input;

    /** @var string */
    private $output;

    public function __construct(string $input, string $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @param array<string, string> $data
     */
    public static function createFromArray(array $data): self
    {
        $missingFields = [];

        if (!isset($data['input'])) {
            $missingFields[] = 'input';
        }

        if (!isset($data['output'])) {
            $missingFields[] = 'output';
        }

        if ([] !== $missingFields) {
            throw new \InvalidArgumentException(sprintf('Missing keys in array: "%s"', implode('","', $missingFields)));
        }

        return new self((string) $data['input'], (string) $data['output']);
    }

    public function getInput(): string
    {
        return $this->input;
    }

    public function getOutput(): string
    {
        return $this->output;
    }
}
