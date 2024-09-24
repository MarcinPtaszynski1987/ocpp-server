<?php
namespace MyApp\Messages\Station;
use MyApp\Responses\Station\Base\StationResponse;

class ChangeAvailabilityStationMessage extends StationResponse{

    private string $Sample_message = '[2,"15455","Heartbeat",{}]';

    public function __construct(private readonly array $data)
    {
        $this->setMessage(null);
    }
    public function getMessageName(): string
    {
        return "Authorize";
    }
}