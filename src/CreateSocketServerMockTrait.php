<?php

declare(strict_types=1);

namespace Chubbyphp\SocketServerMock;

use Symfony\Component\Process\Process;

trait CreateSocketServerMockTrait
{
    private function createSocketServerMock(string $host, int $port, array $messageLogs): Process
    {
        $process = new Process([
            realpath(__DIR__ . '/../bin/socketServerMock'),
            $host,
            $port,
            json_encode($messageLogs),
        ]);

        $process->start();

        $output = '';
        while ($process->isRunning()) {
            $output .= $process->getOutput();
            if (str_contains($output, 'socket server mock: started')) {
                break;
            }

            usleep(10000);
        }

        return $process;
    }
}
