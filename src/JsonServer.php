<?php

namespace Carnage\JsonServer;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

/**
 * Class JsonServer
 * @package Server\Protocol
 */
class JsonServer implements MessageComponentInterface
{
    /**
     * @var \Ratchet\MessageComponentInterface
     */
    protected $wrapped;

    /**
     * @var \SplObjectStorage
     */
    protected $connections;

    /**
     * @param MessageComponentInterface $wrapped
     */
    public function __construct(MessageComponentInterface $wrapped)
    {
        $this->wrapped = $wrapped;
        $this->connections = new \SplObjectStorage();
    }

    /**
     * When a new connection is opened it will be passed to this method
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $wrapper = new JsonConnection($conn);
        $this->connections->attach($conn, $wrapper);

        $this->wrapped->onOpen($wrapper);
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    public function onClose(ConnectionInterface $conn)
    {
        $wrapped = $this->connections->offsetGet($conn);
        $this->wrapped->onClose($wrapped);

        $this->connections->detach($conn);
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param  ConnectionInterface $conn
     * @param  \Exception $e
     * @throws \Exception
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        if ($e instanceof Exception) {
            //@TODO handle exception here
            error_log($e->getMessage());
        } else {
            $wrapped = $this->connections->offsetGet($conn);
            $this->wrapped->onError($wrapped, $e);
        }
    }

    /**
     * Triggered when a client sends data through the socket
     * @param  \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param  string $msg The message received
     * @throws \Exception
     *
     * expects:
     * { 'cmd': "identifier", 'payload': {<data>}}
     * could use bson instead
     */
    function onMessage(ConnectionInterface $from, $msg)
    {
        $from = $this->connections->offsetGet($from);

        if (null === ($json = @json_decode($msg, true))) {
            throw new JsonException();
        }

        if (!is_array($json)) {
            throw new Exception("Invalid message");
        }

        $this->wrapped->onMessage($from, $json);
    }
}