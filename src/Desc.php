<?php

namespace MyApp;

use Exception;
use MyApp\Init;
use MyApp\Responses\Station\AuthorizeStationResponseInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\WebSocket\WsServerInterface;
use SplObjectStorage;

class Desc implements MessageComponentInterface
{
    public SplObjectStorage $clients;
    public \MyApp\Init $init;

    public function __construct()
    {
        $this->init = new Init();
        echo $this->SetLogTxt("WS server started.\r\n\r\n");
    }

    public function SetLogTxt($record)
    {
        return $record;
    }

    public function onOpen(ConnectionInterface $conn): void
    {
        try {
            $this->clients->attach($conn);
            echo $this->SetLogTxt("New connection " . $this->ReturnidTag($conn) . "\r\n");
            $auth_data = $this->init->SelectConected($this->ReturnidTag($conn));
            if ($auth_data['status'] === "Success" && $this->ReturnidTag($conn) !== "") {
                echo $this->SetLogTxt($this->ReturnidTag($conn) . " - " . "The charging station has been authorized\r\n\r\n");
                if (count($this->clients) >= 1) {
                    //Create a new OWN array with a list of connections
                    $sp = 0;
                    foreach ($this->clients as $client) {
                        $allconn[$client->resourceId] = $this->ReturnidTag($client);
                    }
                    /// Count the number of all values in the array, if the values are greater than 1, that is, a match that we need to remove
                    $result = array_count_values($allconn);

                    //Start the loop :)
                    foreach ($result as $key => $value) {
                        //If the current connection is equal to the one in the loop and has a match (greater than 1), then draw attention to this
                        if ($key == $this->ReturnidTag($conn) && $value > 1) {
                            // Loop through the previously created owl array with a list of connections
                            foreach ($allconn as $keys => $val) {
                                if ($val == $this->ReturnidTag($conn)) //If there are matches from the array with the list of connections with the current connection
                                {
                                    if ($conn->resourceId != $keys) {
                                        foreach ($this->clients as $variable) {
                                            if ($val == $this->ReturnidTag($variable)) {
                                                $this->onClose($variable) . ' allconn ' . PHP_EOL . PHP_EOL;
                                                break;
                                            }
                                        }
                                    }
                                }

                            }
                        }
                    }
                }
            } else {
                echo $this->SetLogTxt($this->ReturnidTag($conn) . " - " . "Charging station NOT authorized \r\n\r\n");
                $this->onClose($conn);
            }
        } catch (Exception $ex) {
            echo $this->SetLogTxt("onOpen exception in message " . $ex->getMessage() . "\r\n\r\n");
        }
    }

    public function ReturnidTag($conn)
    {
        $request = $conn->httpRequest;
        $pieces = explode("/", $request->getUri()->getPath());
        $reversed = array_reverse($pieces);
        return $reversed[0];
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        $conn->close();
        echo $this->SetLogTxt("Connection closed " . $this->ReturnidTag($conn) . "\r\n\r\n");
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {

        try {
            foreach ($this->clients as $client) {
                if ($from === $client) {
                    if (is_array(json_decode($msg, true))) {
                        echo $this->SetLogTxt('Message Incoming to Server - ' . $this->ReturnidTag($from) . ' - ' . date('H:i:s') . ' ' . $msg . PHP_EOL . PHP_EOL); //We write the log
                        $response = $this->init->Status(json_decode($msg, true), $this->ReturnidTag($from)); //We process the received command from the charging station
                        $this->init->LogMessageInDb($msg, $this->ReturnidTag($from));
                        if (isset($response['response'])) {
                            $client->send($response['response']);
                            echo $this->SetLogTxt('Message from Server to Charge station - ' . $this->ReturnidTag($from) . ' - ' . date('H:i:s') . ' ' . $response['response'] . PHP_EOL . PHP_EOL); //We write the log
                            $this->init->LogMessageInDb($response['response'], $this->ReturnidTag($from)); //Write the log from the station
                        }
                    } else {
                        echo $this->SetLogTxt("Message $msg not in json format");
                    }
                }
            }// END foreach
        } catch (Exception $ex) {
            echo $this->SetLogTxt("onMessage exception in message $msg " . $ex->getMessage() . "\r\n\r\n");
        }
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
        echo $this->SetLogTxt("Error: {$e->getMessage()}\r\n");
        $conn->close();
    }

}
