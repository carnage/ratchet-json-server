<?php

namespace Carnage\JsonServer;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Ratchet\ConnectionInterface;

class JsonConnectionTest extends MockeryTestCase
{
    public function testSend()
    {
        $mockConn = m::mock(ConnectionInterface::class);
        $mockConn->shouldReceive('send')->with('string');

        $sut = new JsonConnection($mockConn);
        $sut->send('string');
    }

    public function testSendNonScalar()
    {
        $data = ['hello' => 'world'];

        $mockConn = m::mock(ConnectionInterface::class);
        $mockConn->shouldReceive('send')->with(json_encode($data));

        $sut = new JsonConnection($mockConn);
        $sut->send($data);
    }

    public function testClose()
    {
        $mockConn = m::mock(ConnectionInterface::class);
        $mockConn->shouldReceive('close')->withNoArgs();

        $sut = new JsonConnection($mockConn);
        $sut->close();
    }
}
 