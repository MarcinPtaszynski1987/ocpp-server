<?php
namespace MyApp\Responses\Station;
use MyApp\MySQL;
use MyApp\Responses\Station\Base\StationResponse;
use stdClass;

class StartTransactionStationResponse extends StationResponse {

    private string $Sample_message = '[2,"15455","StartTransaction",{"connectorId":1,"idTag":" idTag_Charger","meterStart":0,"timestamp":"2022-11-21T14:55:45.341Z"}]';

    public function __construct(private readonly array $data, private readonly string $stationId, private readonly string $transactionId)
    {
        $tmp = new stdClass();
        $idTagInfo= new stdClass();
        $idTagInfo->parentIdTag = "PARENT_$transactionId";
        $idTagInfo->status = "Accepted";
        $tmp->idTagInfo =$idTagInfo;
        $tmp->transactionId =$transactionId;
        $this->setMessage($tmp,$this->data[1] );
    }


}