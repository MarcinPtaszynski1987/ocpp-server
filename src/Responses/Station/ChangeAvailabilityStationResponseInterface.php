<?php
namespace MyApp\Responses\Station;
use MyApp\Responses\Station\Base\StationResponse;
use stdClass;

class ChangeAvailabilityStationResponseInterface extends StationResponse {

    private string $Sample_message = '[2,"01221201194032","Authorize",{"idTag":"D0431F35"}]';

    public function __construct(private readonly string $idTag = "")
    {
        $tmp = new stdClass();
        $idTagInfo= new stdClass();
        $idTagInfo->status = "Accepted";
        $tmp->idTagInfo =$idTagInfo;


        $this->setMessage($tmp, $idTag);
    }

}