<?php
namespace MyApp\Messages\Station;
use MyApp\Responses\Station\DataTransferStationResponseInterface;
use MyApp\Messages\Station\Base\StationMessageHandler;

class DataTransferStationMessage extends StationMessageHandler{

    private string $Sample_message = '[2,"15455","Heartbeat",{}]';

    public function __construct(private readonly array $data)
    {
        $this->setResponse(new DataTransferStationResponseInterface($data));
    }
    public function getMessageName(): string
    {
        return "Authorize";
    }
}