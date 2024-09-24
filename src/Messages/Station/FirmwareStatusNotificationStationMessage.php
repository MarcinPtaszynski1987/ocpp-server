<?php
namespace MyApp\Messages\Station;
use MyApp\Messages\Station\Base\StationMessageHandler;
use MyApp\Responses\Station\FirmwareStatusNotificationStationResponseInterface;

class FirmwareStatusNotificationStationMessage extends StationMessageHandler{

    private string $Sample_message = '[2,"15455","Heartbeat",{}]';

    public function __construct(private readonly array $data)
    {
        $this->setResponse(new FirmwareStatusNotificationStationResponseInterface($data));
    }
}