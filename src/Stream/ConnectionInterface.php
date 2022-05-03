<?php

declare(strict_types=1);

namespace Chubbyphp\SocketServerMock\Stream;

interface ConnectionInterface
{
    /**
     * @param int<0, max> $length
     */
    public function read(int $length): string;

    public function write(string $string): void;
}
