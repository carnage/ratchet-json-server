<?php
namespace Carnage\JsonServer;

use Ratchet\ConnectionInterface;

final class JsonConnection implements ConnectionInterface
{
    private $wrapped;

    public function __construct(ConnectionInterface $wrapped)
    {
        $this->wrapped = $wrapped;
    }

    /**
     * Send data to the connection
     * @param  array $data
     * @return \Ratchet\ConnectionInterface
     */
    function send($data)
    {
        if (!is_scalar($data)) {
            $data = json_encode($data);
        }

        $this->wrapped->send($data);
    }

    /**
     * Close the connection
     */
    function close()
    {
        $this->wrapped->close();
    }
}
