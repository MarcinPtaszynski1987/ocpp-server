<?php
//Forgive me my friend, but I can't make part of this file publicly available. 
//This is a part of my life on which I spent a lot of time. 
//If you want to get the source file or my advice, write to me in direct https://www.instagram.com/gennadiy.gnezdilov/

namespace MyApp;
use MyApp\Messages\Station\AuthorizeStationMessage;
use MyApp\Parsers\StationMessageParser;
use MyApp\Parsers\StationResponseParser;



class Init
{
  private $db;

  function __construct()
	{
      $this->db = new MySQL(DB_HOST, DB_PORT, DB_USER, DB_PASS, DB_NAME);
	}

  //Check if there are permissions for the charging station to connect to our server
  public function SelectConected($idTag): array
	{
        return  (new AuthorizeStationMessage($idTag, $this->db))->parse();

    }
  //Check if there are permissions for the charging station to connect to our server

  //Ping the my_set_command database and if there is a command, send the command to the charging station, then delete the command and the database
  public function SetCommand($idTag): mixed
  {
        $sql = "SELECT * FROM ocpp_messages_to_send WHERE charger_idTag = '$idTag' AND status LIKE 'NEW' LIMIT 1 ";
        return $this->db->query($sql);
  }

  //Write to the temporary base who launched the command
  public function UpUserCommand($id =0): void
	{
        $sql = "UPDATE ocpp_messages_to_send SET status = 'SEND', sended_at = NOW()  WHERE id = $id ";
        $this->db->query($sql);
  }

  //Write to the database, the history of commands sent manually
  public function up_command($data, $idTag): void
  {
        $sql = "INSERT INTO ocpp_messages (message) VALUES ('$idTag -> $data')";
        $this->db->query($sql);

  }
  //Write to the database, the history of commands sent manually

  //We receive some data from the station and process it through the switch - we answer
  public function Status($data, $idTag = ''): array
  {
        $parser = intval($data[0]) == 2 ? new StationMessageParser($data, $idTag, $this->db) : new StationResponseParser($data);
        return $parser->parse();
  }

  
}
