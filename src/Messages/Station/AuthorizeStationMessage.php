<?php
namespace MyApp\Messages\Station;
use MyApp\Messages\Station\Base\StationMessageHandler;
use MyApp\MySQL;
use MyApp\Responses\Station\AuthorizeStationResponseInterface;
class AuthorizeStationMessage extends StationMessageHandler{

    public function __construct(private readonly string $idTag = "", MySQL $db)
    {
        if ($idTag !== "") {
            $sql = "SELECT bakend_tag FROM ocpp_charges_tags WHERE bakend_tag = '$idTag' LIMIT 1";
            $in_db = $db->query($sql);
            if (count($in_db) == 0) {
                $status = "Invalid";
            } else {
                $sql = "UPDATE ocpp_charges_tags set status = 'Accepted', last_action ='Authorize' WHERE bakend_tag = '$idTag'";
                $db->query($sql);
                $status = "Accepted";
            }
        }else{
            $status = "Invalid";
        }
        $this->setResponse(new AuthorizeStationResponseInterface($this->idTag, $status));
    }

}