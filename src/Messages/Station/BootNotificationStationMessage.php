<?php
namespace MyApp\Messages\Station;
use MyApp\Responses\Station\BootNotificationStationResponseInterface;
use MyApp\Messages\Station\Base\StationMessageHandler;

class BootNotificationStationMessage extends StationMessageHandler{

    private string $Sample_message = '[2,"15455","Heartbeat",{}]';

    public function __construct(private readonly array $data)
    {
        $this->setResponse(new BootNotificationStationResponseInterface($this->data));
    }
    public function getMessageName(): string
    {
        return "Authorize";
    }

}