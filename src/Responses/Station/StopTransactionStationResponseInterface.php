<?php
namespace MyApp\Responses\Station;
use MyApp\MySQL;
use MyApp\Responses\Station\Base\StationResponse;
use stdClass;

class StopTransactionStationResponseInterface extends StationResponse {

    private string $Sample_message = '[2,"01221201194032","Authorize",{"idTag":"D0431F35"}]';

    public function __construct(private readonly array $data, private readonly string $stationId)
    {
        $tmp = new stdClass();
        $idTagInfo= new stdClass();
        $idTagInfo->parentIdTag = "PARENT_".$this->data[3]['transactionId'];
        $idTagInfo->status = "Accepted";
        $tmp->idTagInfo =$idTagInfo;
        $this->setMessage($tmp,$this->data[1] );
    }

}