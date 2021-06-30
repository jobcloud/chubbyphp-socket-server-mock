<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\SocketServerMock\Unit\Stream;

use Chubbyphp\SocketServerMock\Stream\ServerFactory;
use Chubbyphp\SocketServerMock\Stream\ServerInterface;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\SocketServerMock\Stream\ServerFactory
 *
 * @internal
 */
final class ServerFactoryTest extends TestCase
{
    use PHPMock;

    public function testCreateByHostAndPort(): void
    {
        $serverResource = new \stdClass();

        $stream_socket_server = $this->getFunctionMock('Chubbyphp\SocketServerMock\Stream', 'stream_socket_server');
        $stream_socket_server->expects($this->once())->with('tcp://0.0.0.0:3000')->willReturn($serverResource);

        $is_resource = $this->getFunctionMock('Chubbyphp\SocketServerMock\Stream', 'is_resource');
        $is_resource->expects($this->once())->with($serverResource)->willReturn(true);

        $fwrite = $this->getFunctionMock('Chubbyphp\SocketServerMock\Stream', 'fwrite');
        $fwrite->expects($this->once())->with(STDOUT, 'socket server mock: started' . PHP_EOL);

        $factory = new ServerFactory();

        self::assertInstanceOf(ServerInterface::class, $factory->createByHostAndPort('0.0.0.0', 3000));
    }
}
