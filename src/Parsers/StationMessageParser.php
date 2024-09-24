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
use MyApp\MySQL;
use ReflectionExtension;

class StationMessageParser {

    private StationMessage $messageHandler;
    private array $data;
    private string $idTag;
    public ?MySQL $db;

    public function __construct($data, $idTag = "", MySQL $db = null)
    {
        $this->data = $data;
        $this->idTag = $idTag;
        $this->db = $db;
        $this->setupMessageType();

    }

    /**
     * @return void
     * @throws \Exception
     */
    private function setupMessageType(): void
    {

        if (isset($this->data[2])) {
            $swith_by = $this->data[2];
        } elseif ($this->data['action']) {
            $swith_by = $this->data['action'];
        }

        $this->messageHandler = match ($swith_by) {
            "Authorize" => new AuthorizeStationMessage($this->data[1], $this->db),
            "BootNotification" => new BootNotificationStationMessage($this->data),
            "ChangeAvailability" => new ChangeAvailabilityStationMessage($this->data),
            "ChangeConfiguration" => new ChangeConfigurationStationMessage($this->data),
            "ClearCache" => new ClearCacheStationMessage($this->data),
            "DataTransfer" => new DataTransferStationMessage($this->data),
            "GetConfigurationStationMessage" => new GetConfigurationStationMessage($this->data),
            "Heartbeat" => new HeartbeatStationMessage($this->data[1]),
            "MeterValues" => new MeterValuesStationMessage($this->data, $this->idTag, $this->db),
            "StartTransaction" => new StartTransactionStationMessage($this->data, $this->idTag, $this->db),
            "StatusNotification" => new StatusNotificationStationMessage($this->data, $this->idTag),
            "StopTransaction" => new StopTransactionStationMessage($this->data, $this->idTag, $this->db),
            "DiagnosticsStatusNotification" => new DiagnosticsStatusNotificationStationMessage($this->data),
            "FirmwareStatusNotification" => new FirmwareStatusNotificationStationMessage($this->data),
            default => throw new \Exception('Unexpected value'),
        };
    }
    public function parse(): array{
       return $this->messageHandler->parse();
    }
}