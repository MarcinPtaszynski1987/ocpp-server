<?php
namespace MyApp\Messages\Station;
use MyApp\Messages\Station\Base\StationMessageHandler;
use MyApp\Responses\Station\HeartbeatStationResponseInterface;

class HeartbeatStationMessage extends StationMessageHandler{

    public function __construct(private readonly string $idTag = "")
    {
        $this->setResponse(new HeartbeatStationResponseInterface($idTag));
        $this->sampleMessage ='[2,"15455","Heartbeat",{}]';
    }
    public function getMessageName(): string
    {
        return "Authorize";
    }

}