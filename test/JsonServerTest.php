<?php

namespace Carnage\JsonServer;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

/**
 * Class JsonServerTest
 * @package Carnage\JsonServer
 * @TODO test error conditions + error handling
 */
class JsonServerTest extends MockeryTestCase
{
    public function testOnOpen()
    {
        $mockMCI = m::mock(MessageComponentInterface::class);
        $mockMCI->shouldReceive('onOpen')->with(m::type(JsonConnection::class));
        $mockConn = m::mock(ConnectionInterface::class);

        $sut = new JsonServer($mockMCI);

        $sut->onOpen($mockConn);
    }

    public function testOnClose()
    {
        $mockMCI = m::mock(MessageComponentInterface::class);
        $mockMCI->shouldReceive('onOpen');
        $mockMCI->shouldReceive('onClose')->with(m::type(JsonConnection::class));

        $mockConn = m::mock(ConnectionInterface::class);

        $sut = new JsonServer($mockMCI);

        $sut->onOpen($mockConn);
        $sut->onClose($mockConn);
    }

    public function testOnMessage()
    {
        $data = ['hello' => 'world'];
        $mockMCI = m::mock(MessageComponentInterface::class);
        $mockMCI->shouldReceive('onOpen');
        $mockMCI->shouldReceive('onMessage')->with(m::type(JsonConnection::class), $data);

        $mockConn = m::mock(ConnectionInterface::class);

        $sut = new JsonServer($mockMCI);

        $sut->onOpen($mockConn);
        $sut->onMessage($mockConn, json_encode($data));
    }

    /**
     * @expectedException \Carnage\JsonServer\Exception
     */
    public function testOnInvalidMessage()
    {
        $data = '';
        $mockMCI = m::mock(MessageComponentInterface::class);
        $mockMCI->shouldReceive('onOpen');

        $mockConn = m::mock(ConnectionInterface::class);

        $sut = new JsonServer($mockMCI);

        $sut->onOpen($mockConn);
        $sut->onMessage($mockConn, json_encode($data));
    }

    /**
     * @expectedException \Carnage\JsonServer\JsonException
     */
    public function testOnInvalidJson()
    {
        $data = '{"key":"';
        $mockMCI = m::mock(MessageComponentInterface::class);
        $mockMCI->shouldReceive('onOpen');

        $mockConn = m::mock(ConnectionInterface::class);

        $sut = new JsonServer($mockMCI);

        $sut->onOpen($mockConn);
        $sut->onMessage($mockConn, $data);
    }

    public function testOnError()
    {
        $e = new \Exception('error');

        $mockMCI = m::mock(MessageComponentInterface::class);
        $mockMCI->shouldReceive('onOpen');
        $mockMCI->shouldReceive('onError')->with(m::type(JsonConnection::class), $e);

        $mockConn = m::mock(ConnectionInterface::class);

        $sut = new JsonServer($mockMCI);

        $sut->onOpen($mockConn);
        $sut->onError($mockConn, $e);
    }
}
 