<?php

declare(strict_types=1);

namespace Chubbyphp\SocketServerMock\Stream;

final class Connection implements ConnectionInterface
{
    /** @var resource */
    private $stream;

    public function __construct(ServerInterface $socketServer)
    {
        $stream = stream_socket_accept($socketServer());

        if (!is_resource($stream)) {
            throw StreamException::createFromStreamAcceptError();
        }

        $this->stream = $stream;
    }

    public function __deconstruct(): void
    {
        fclose($this->stream);
    }

    public function read(int $length): string
    {
        return (string) fread($this->stream, $length);
    }

    public function write(string $string): void
    {
        fwrite($this->stream, $string);
    }
}
