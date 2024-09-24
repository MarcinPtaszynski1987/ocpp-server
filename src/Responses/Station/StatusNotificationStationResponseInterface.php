<?php
namespace MyApp\Responses\Station;
use MyApp\Responses\Station\Base\StationResponse;
use stdClass;

class StatusNotificationStationResponseInterface extends StationResponse {

    private string $Sample_message = '[2,"15455","StatusNotification",{"connectorId":0,"errorCode":"NoError","status":"Available","timestamp":"2022-12-02T15:08:43.001Z"}]';

    public function __construct(private readonly array $data, private readonly string $stationId)
    {
        $this->setMessage(new stdClass,$this->data[1]);
    }

}