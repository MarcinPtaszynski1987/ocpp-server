<?php
namespace MyApp\Responses\Station;
use MyApp\Responses\Station\Base\StationResponse;
use stdClass;

class MeterValuesStationResponseInterface extends StationResponse {

    private string $Sample_message = '2,"15455",{"connectorId":1,"transactionId":0,"meterValue":[{"timestamp":"2022-11-21T20:50:00.001Z","sampledValue":[{"value":"19309.971","unit":"Wh","context":"Sample.Clock","format":"Raw","measurand":"Energy.Active.Import.Register","location":"Outlet"}]}]}]';


    public function __construct(private readonly array $data)
    {
        $this->setMessage(new stdClass,$this->data[1] );
    }
}