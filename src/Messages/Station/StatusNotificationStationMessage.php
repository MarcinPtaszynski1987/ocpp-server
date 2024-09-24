<?php
namespace MyApp\Messages\Station;
use MyApp\Messages\Station\Base\StationMessageHandler;
use MyApp\Responses\Station\StatusNotificationStationResponseInterface;

class StatusNotificationStationMessage extends StationMessageHandler{

    private string $Sample_message = '[2,"15455","Heartbeat",{}]';

    public function __construct(private readonly array $data, private readonly string $stationId )
    {
        $this->setResponse(new StatusNotificationStationResponseInterface($this->data, $this->stationId));
    }
    public function getMessageName(): string
    {
        return "Authorize";
    }
}