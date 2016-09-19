# ratchet-json-server
An extension for ratchet enabling communication via json serialised strings

Wrapped by Ratchet's Websocket Server, Wraps your app. 

Will automatically serialise and unserialise json into php arrays and back again

# Install:

composer require carnage/ratchet-json-server ~0.1

# Usage:

```
<?php
// Your shell script
use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Carnage\JsonServer\JsonServer;

$ws = new WsServer(new JsonServer(new MyChat));
$ws->disableVersion(0); // old, bad, protocol version

// Make sure you're running this as root
$server = IoServer::factory(new HttpServer($ws));
$server->run();
```

MyChat can now send arrays of data using $conn->send(['name' => 'Fred', 'message' => 'hi']); and have them automatically json serialised. Json strings coming in from your client will be deserialised and presented to the onMessage method of MyChat as an array. 
