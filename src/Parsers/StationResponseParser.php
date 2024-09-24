<?php

namespace MyApp\Parsers;

use MyApp\Messages\Station\AuthorizeStationMessage;
use MyApp\Messages\Station\Base\StationMessage;
use MyApp\Messages\Station\Base\StationMessageHandler;
use MyApp\Messages\Station\BootNotificationStationMessage;
use MyApp\Messages\Station\ChangeAvailabilityStationMessage;
use MyApp\Messages\Station\ClearCacheStationMessage;
use MyApp\Messages\Station\DataTransferStationMessage;
use MyApp\Messages\Station\DiagnosticsStatusNotificationStationMessage;
use MyApp\Messages\Station\FirmwareStatusNotificationStationMessage;
use MyApp\Messages\Station\GetConfigurationStationMessage;
use MyApp\Messages\Station\HeartbeatStationMessage;
use MyApp\Messages\Station\MeterValuesStationMessage;
use MyApp\Messages\Station\StartTransactionStationMessage;
use MyApp\Messages\Station\StatusNotificationStationMessage;
use MyApp\Messages\Station\StopTransactionStationMessage;
use MyApp\Messages\Station\ChangeConfigurationStationMessage;
use ReflectionExtension;

class StationResponseParser {

    private StationMessage $messageHandler;
    private array $data;
    private string $idTag;
    public function __construct($data)
    {
        $this->data = $data;
        $this->setupMessageType();
    }

    private function setupMessageType()
    {

    }
    public function parse(): array{
        //check if its resposne after start remote trasaction
       return array();
    }
}