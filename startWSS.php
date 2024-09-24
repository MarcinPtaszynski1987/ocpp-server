<?php
//Ratchet Chat WSS server
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\Socket\SecureServer;

use MyApp\Desc;
use MyApp\MySQL;
use MyApp\Init;

require 'vendor/autoload.php';
include('src/config/define.php');
$loop = React\EventLoop\Loop::get();
$server = new React\Socket\SocketServer('0.0.0.0:2060', array(),$loop);

$secureServer = new SecureServer($server, $loop, [
    'local_cert'  => '/etc/letsencrypt/live/admin-watt-testing.wattcharge.io/fullchain.pem',
    'local_pk' => '/etc/letsencrypt/live/admin-watt-testing.wattcharge.io/privkey.pem',
    'verify_peer' => false,
]);



$socket = new Desc();
$httpServer = new HttpServer(new WsServer( $socket  ));

$ioServer = new IoServer($httpServer, $secureServer, $loop);
//When the wss connection is active, start a timer to check and send commands to the station
$ioServer->loop->addPeriodicTimer(5, function () use ($socket)
{
  foreach($socket->clients as $client)
  {
    $init = new Init();
    $ToSend = $init->SetCommand($socket->ReturnidTag($client));
    if(count($ToSend))
    {
        $send = $ToSend[0];
        if ($send['charger_idTag'] === $socket->ReturnidTag($client))
        {
          $init->UpUserCommand($send['id']);
          echo 'CS - user_id - '.$send['id'].' - '.$socket->ReturnidTag($client).' '.date('H:i:s').' '.$send['message'].PHP_EOL.PHP_EOL;
          $client->send($send['message']);
          $init->up_command($send['message'], $socket->ReturnidTag($client));
        }
    }
  }
});
//When the wss connection is active, start a timer to check and send commands to the station

$ioServer->run();
