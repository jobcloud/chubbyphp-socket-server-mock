<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\SocketServerMock\Unit;

use Chubbyphp\SocketServerMock\CreateSocketServerMockTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

/**
 * @covers \Chubbyphp\SocketServerMock\CreateSocketServerMockTrait
 *
 * @internal
 */
final class CreateSocketServerMockTraitTest extends TestCase
{
    use CreateSocketServerMockTrait;

    public function testWithExpectedInput(): void
    {
        $process = $this->createSocketServerMock('0.0.0.0', 62000, [[['input' => 'input', 'output' => 'output']]]);

        self::assertTrue($process->isRunning());

        $errno = 0;
        $errstr = '';

        $stream = stream_socket_client('tcp://localhost:62000', $errno, $errstr, 30);

        fwrite($stream, 'input');

        self::assertSame('output', fread($stream, strlen('output')));

        $process->wait();

        self::assertSame(0, $process->getExitCode());
    }

    public function testWithUnexpectedInput(): void
    {
        $process = $this->createSocketServerMock('0.0.0.0', 62000, [[['input' => 'input', 'output' => 'output']]]);

        $errno = 0;
        $errstr = '';

        $stream = stream_socket_client('tcp://localhost:62000', $errno, $errstr, 30);

        fwrite($stream, 'inu');

        $process->wait();

        self::assertSame(200, $process->getExitCode());
    }

    public function testMissingHost(): void
    {
        $process = new Process([realpath(__DIR__ . '/../../bin/socketServerMock')]);
        $process->start();
        $process->wait();

        self::assertSame(1, $process->getExitCode());
        self::assertSame('Missing host' . PHP_EOL, $process->getErrorOutput());
    }

    public function testMissingPort(): void
    {
        $process = new Process([
            realpath(__DIR__ . '/../../bin/socketServerMock'),
            '0.0.0.0',
        ]);
        $process->start();
        $process->wait();

        self::assertSame(2, $process->getExitCode());
        self::assertSame('Missing port' . PHP_EOL, $process->getErrorOutput());
    }

    public function testMissingMessageLogs(): void
    {
        $process = new Process([
            realpath(__DIR__ . '/../../bin/socketServerMock'),
            '0.0.0.0',
            63000,
        ]);
        $process->start();
        $process->wait();

        self::assertSame(3, $process->getExitCode());
        self::assertSame('Missing message logs' . PHP_EOL, $process->getErrorOutput());
    }

    public function testWithStringAsPort(): void
    {
        $process = new Process([
            realpath(__DIR__ . '/../../bin/socketServerMock'),
            '0.0.0.0',
            'test',
            '[[{"input":"input","output":"output"}]]',
        ]);
        $process->start();
        $process->wait();

        self::assertSame(4, $process->getExitCode());
        self::assertSame('Port "test" is not an integer' . PHP_EOL, $process->getErrorOutput());
    }

    public function testWithInvalidMessageLogsJson(): void
    {
        $process = new Process([
            realpath(__DIR__ . '/../../bin/socketServerMock'),
            '0.0.0.0',
            '63000',
            'json',
        ]);
        $process->start();
        $process->wait();

        self::assertSame(5, $process->getExitCode());
        self::assertSame('Invalid json: Syntax error' . PHP_EOL, $process->getErrorOutput());
    }
}
