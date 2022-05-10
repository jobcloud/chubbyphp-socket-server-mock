<?php

declare(strict_types=1);

namespace Chubbyphp\SocketServerMock;

use Chubbyphp\SocketServerMock\Stream\ConnectionInterface;
use Chubbyphp\SocketServerMock\Stream\ServerFactoryInterface;
use Chubbyphp\SocketServerMock\Stream\ServerInterface;

final class SocketServerMock
{
    /** @var ServerFactoryInterface */
    private $serverFactory;

    public function __construct(ServerFactoryInterface $serverFactory)
    {
        $this->serverFactory = $serverFactory;
    }

    public function run(string $host, int $port, MessageLogsInterface $messageLogs): void
    {
        $socketServer = $this->serverFactory->createByHostAndPort($host, $port);

        $this->processMessageLogs($socketServer, $messageLogs);
    }

    private function processMessageLogs(ServerInterface $socketServer, MessageLogsInterface $messageLogs): void
    {
        while (null !== $messageLog = $messageLogs->getNextMessageLog()) {
            $socketConnection = $socketServer->createConnection();

            $this->processMessageLog($socketConnection, $messageLog);
        }
    }

    private function processMessageLog(ConnectionInterface $socketConnection, MessageLogInterface $messageLog): void
    {
        while (null !== $message = $messageLog->getNextMessage()) {
            $input = $this->getDecodedValue($message->getInput());

            $givenInput = '';
            while (true) {
                $givenInput .= $socketConnection->read(1);

                if (!str_starts_with($input, $givenInput)) {
                    throw SocketServerMockException::createByInvalidInput($givenInput, $input);
                }

                if ($input === $givenInput) {
                    $socketConnection->write($this->getDecodedValue($message->getOutput()));

                    continue 2;
                }
            }
        }
    }

    private function getDecodedValue(string $value): string
    {
        if (str_starts_with($value, 'base64:')) {
            return base64_decode(substr($value, 7));
        }

        return $value;
    }
}
