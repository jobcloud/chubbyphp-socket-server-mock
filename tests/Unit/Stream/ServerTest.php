<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\SocketServerMock\Unit\Stream;

use Chubbyphp\SocketServerMock\Stream\ConnectionInterface;
use Chubbyphp\SocketServerMock\Stream\Server;
use Chubbyphp\SocketServerMock\Stream\StreamException;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\SocketServerMock\Stream\Server
 *
 * @internal
 */
final class ServerTest extends TestCase
{
    use PHPMock;

    public function testSuccessfulConstruct(): void
    {
        $serverResource = new \stdClass();

        $stream_socket_server = $this->getFunctionMock('Chubbyphp\SocketServerMock\Stream', 'stream_socket_server');
        $stream_socket_server->expects($this->once())->with('tcp://0.0.0.0:3000')->willReturn($serverResource);

        $is_resource = $this->getFunctionMock('Chubbyphp\SocketServerMock\Stream', 'is_resource');
        $is_resource->expects($this->once())->with($serverResource)->willReturn(true);

        $fwrite = $this->getFunctionMock('Chubbyphp\SocketServerMock\Stream', 'fwrite');
        $fwrite->expects($this->once())->with(STDOUT, 'socket server mock: started' . PHP_EOL);

        new Server('0.0.0.0', 3000);
    }

    public function testFailedConstruct(): void
    {
        $this->expectException(StreamException::class);
        $this->expectExceptionMessage('Server could not listen to host: "0.0.0.0", port: 3000, detail: ""');

        $serverResource = new \stdClass();

        $stream_socket_server = $this->getFunctionMock('Chubbyphp\SocketServerMock\Stream', 'stream_socket_server');
        $stream_socket_server->expects($this->once())->with('tcp://0.0.0.0:3000')->willReturn($serverResource);

        $is_resource = $this->getFunctionMock('Chubbyphp\SocketServerMock\Stream', 'is_resource');
        $is_resource->expects($this->once())->with($serverResource)->willReturn(false);

        new Server('0.0.0.0', 3000);
    }

    public function testCreateConnection(): void
    {
        $serverResource = new \stdClass();
        $connectionResource = new \stdClass();

        $stream_socket_server = $this->getFunctionMock('Chubbyphp\SocketServerMock\Stream', 'stream_socket_server');
        $stream_socket_server->expects($this->once())->with('tcp://0.0.0.0:3000')->willReturn($serverResource);

        $stream_socket_accept = $this->getFunctionMock('Chubbyphp\SocketServerMock\Stream', 'stream_socket_accept');
        $stream_socket_accept->expects($this->once())->with($serverResource)->willReturn($connectionResource);

        $is_resource = $this->getFunctionMock('Chubbyphp\SocketServerMock\Stream', 'is_resource');
        $is_resource
            ->expects(self::exactly(2))
            ->withConsecutive([$serverResource], [$connectionResource])
            ->willReturn(true);

        $fwrite = $this->getFunctionMock('Chubbyphp\SocketServerMock\Stream', 'fwrite');
        $fwrite->expects($this->once())->with(STDOUT, 'socket server mock: started' . PHP_EOL);

        $server = new Server('0.0.0.0', 3000);

        self::assertInstanceOf(ConnectionInterface::class, $server->createConnection());
    }

    public function testDestruct(): void
    {
        $serverResource = new \stdClass();

        $stream_socket_server = $this->getFunctionMock('Chubbyphp\SocketServerMock\Stream', 'stream_socket_server');
        $stream_socket_server->expects($this->once())->with('tcp://0.0.0.0:3000')->willReturn($serverResource);

        $is_resource = $this->getFunctionMock('Chubbyphp\SocketServerMock\Stream', 'is_resource');
        $is_resource->expects($this->once())->with($serverResource)->willReturn(true);

        $fwrite = $this->getFunctionMock('Chubbyphp\SocketServerMock\Stream', 'fwrite');
        $fwrite->expects($this->once())->with(STDOUT, 'socket server mock: started' . PHP_EOL);

        $fclose = $this->getFunctionMock('Chubbyphp\SocketServerMock\Stream', 'fclose');
        $fclose->expects($this->once())->with($serverResource);

        $server = new Server('0.0.0.0', 3000);
        $server->__deconstruct();
    }

    public function testInvoke(): void
    {
        $serverResource = new \stdClass();

        $stream_socket_server = $this->getFunctionMock('Chubbyphp\SocketServerMock\Stream', 'stream_socket_server');
        $stream_socket_server->expects($this->once())->with('tcp://0.0.0.0:3000')->willReturn($serverResource);

        $is_resource = $this->getFunctionMock('Chubbyphp\SocketServerMock\Stream', 'is_resource');
        $is_resource->expects($this->once())->with($serverResource)->willReturn(true);

        $fwrite = $this->getFunctionMock('Chubbyphp\SocketServerMock\Stream', 'fwrite');
        $fwrite->expects($this->once())->with(STDOUT, 'socket server mock: started' . PHP_EOL);

        $server = new Server('0.0.0.0', 3000);

        self::assertSame($serverResource, $server());
    }
}
