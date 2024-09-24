<?php
namespace MyApp\Responses\Station;
use MyApp\Responses\Station\Base\StationResponse;
use stdClass;

class AuthorizeStationResponseInterface extends StationResponse {
    public function __construct(private readonly string $idTag = "", private readonly string $status)
    {
        $tmp = new stdClass();
        $idTagInfo= new stdClass();
        $idTagInfo->status = $this->status;
        $tmp->idTagInfo =$idTagInfo;
        $this->setMessage($tmp, $this->idTag);
    }


}