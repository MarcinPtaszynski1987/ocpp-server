<?php
namespace MyApp;

use Exception;
use MyApp\Responses\Station\AuthorizeStationResponseInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\WebSocket\WsServerInterface;
use MyApp\Init;
use SplObjectStorage;

class Desc implements MessageComponentInterface
{
    public $clients;
    public $init;

    public function __construct()
    {
        $this->clients = new SplObjectStorage;
        echo $this->SetLogTxt("WS server started.\r\n\r\n");
    }

    public function SetLogTxt($record)
    {
      //$filename = __DIR__.'/server.log';
      //file_put_contents($filename, $record, FILE_APPEND | LOCK_EX);
      return $record;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        try {
          $this->clients->attach($conn);
          echo $this->SetLogTxt("New connection ".$this->ReturnidTag($conn)."\r\n");

          $init = new Init();
          $auth_data = $init->SelectConected($this->ReturnidTag($conn));
          if($auth_data['status'] === "Success" && $this->ReturnidTag($conn) !== "")
          {
            echo $this->SetLogTxt($this->ReturnidTag($conn)." - "."The charging station has been authorized\r\n\r\n");
            $this->init = $init;

            //Sometimes it happens that several open WebSocket channels are created for one charging station, our task is to delete old connections
            //If the number of connections is greater than or equal to 1
            if(count($this->clients) >= 1)
            {
              //Create a new OWN array with a list of connections
              $sp = 0;
              foreach($this->clients as $client)
              {
                $allconn[$client->resourceId] = $this->ReturnidTag($client);
              }

              /// Count the number of all values in the array, if the values are greater than 1, that is, a match that we need to remove
              $result = array_count_values($allconn);

              //Start the loop :)
              foreach ($result as $key => $value)
              {
                //If the current connection is equal to the one in the loop and has a match (greater than 1), then draw attention to this
                if($key == $this->ReturnidTag($conn) && $value > 1)
                {
                    echo 'Worth paying attention here '.$this->ReturnidTag($conn).PHP_EOL.PHP_EOL;
                    // Loop through the previously created owl array with a list of connections
                    foreach ($allconn as $keys => $val)
                    {
                      if( $val == $this->ReturnidTag($conn)) //If there are matches from the array with the list of connections with the current connection
                      {
                          if($conn->resourceId != $keys)
                          {
                             foreach ($this->clients as $variable)
                             {
                                if($val == $this->ReturnidTag($variable))
                                {
                                  echo $val.' going to be removed '.$keys.PHP_EOL.PHP_EOL;
                                  $this->onClose($variable).' allconn '.PHP_EOL.PHP_EOL; break;
                                }
                             }
                          }
                      }

                    }
                }
              }
            }

          }
          else
          {
            echo $this->SetLogTxt($this->ReturnidTag($conn)." - "."Charging station NOT authorized \r\n\r\n");
            $this->onClose($conn);
          }
        } catch (Exception $ex){
            echo $this->SetLogTxt("onOpen exception in message ".$ex->getMessage()."\r\n\r\n");
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
        echo $this->SetLogTxt("Connection closed ".$this->ReturnidTag($conn)."\r\n\r\n");
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {

        try {
           foreach ($this->clients as $client)
           {
              if ($from === $client)
              {
                if(is_array(json_decode($msg,true)))
                {
                  $init = new Init();
                  echo $this->SetLogTxt('Message Incoming to Server - '.$this->ReturnidTag($from).' - '.date('H:i:s').' '.$msg.PHP_EOL.PHP_EOL); //We write the log
                  $respon = $init->Status(json_decode($msg,true), $this->ReturnidTag($from)); //We process the received command from the charging station
                  $init->up_command($msg, $this->ReturnidTag($from)); //Write the log from the station

                  if(isset($respon['response']) && !is_null($respon['response'])) //If the method is not defined on the server, we work it out and write the command
                  {
                    $client->send($respon['response']);
                    echo $this->SetLogTxt('Message from server to Charge station - '.$this->ReturnidTag($from).' - '.date('H:i:s').' '.$respon['response'].PHP_EOL.PHP_EOL); //We write the log
                    $init->up_command($respon['response'], $this->ReturnidTag($from)); //Write the log from the station
                  }
                }else{
                    echo $this->SetLogTxt("Message $msg not in json format");
                } //END if is_array
              }//END $from === $client
            }// END foreach
        } catch (Exception $ex){
            echo $this->SetLogTxt("onMessage exception in message $msg ".$ex->getMessage()."\r\n\r\n");
        }
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
        echo $this->SetLogTxt("Error: {$e->getMessage()}\r\n");
        $conn->close();
    }

}
