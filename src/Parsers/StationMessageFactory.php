<?php

namespace MyApp\Parsers;

use MyApp\Messages\Station\AuthorizeStationMessage;
use MyApp\Messages\Station\Base\StationMessage;
use MyApp\Messages\Station\BootNotificationStationMessage;
use MyApp\Messages\Station\ChangeAvailabilityStationMessage;
use MyApp\Messages\Station\DataTransferStationMessage;
use MyApp\Messages\Station\DiagnosticsStatusNotificationStationMessage;
use MyApp\Messages\Station\FirmwareStatusNotificationStationMessage;
use MyApp\Messages\Station\HeartbeatStationMessage;
use MyApp\Messages\Station\MeterValuesStationMessage;
use MyApp\Messages\Station\StartTransactionStationMessage;
use MyApp\Messages\Station\StatusNotificationStationMessage;
use MyApp\Messages\Station\StopTransactionStationMessage;

class StationMessageFactory
{
    private array $messageHandlers = [];

    public function __construct()
    {
        // Register handlers (open for extension, but we don't modify this method)
        $this->registerHandler('Authorize', fn($data, $idTag, $db) => new AuthorizeStationMessage($data[1], $db));
        $this->registerHandler('BootNotification', fn($data, $idTag, $db) => new BootNotificationStationMessage($data));
        $this->registerHandler('ChangeAvailability', fn($data, $idTag, $db) => new ChangeAvailabilityStationMessage($data));
        $this->registerHandler('DataTransfer', fn($data, $idTag, $db) => new DataTransferStationMessage($data));
        $this->registerHandler('Heartbeat', fn($data, $idTag, $db) => new HeartbeatStationMessage($data[1]));
        $this->registerHandler('MeterValues', fn($data, $idTag, $db) => new MeterValuesStationMessage($data, $idTag, $db));
        $this->registerHandler('StartTransaction', fn($data, $idTag, $db) => new StartTransactionStationMessage($data, $idTag, $db));
        $this->registerHandler('StatusNotification', fn($data, $idTag, $db) => new StatusNotificationStationMessage($data, $idTag));
        $this->registerHandler('StopTransaction', fn($data, $idTag, $db) => new StopTransactionStationMessage($data, $idTag, $db));
        $this->registerHandler('DiagnosticsStatusNotification', fn($data, $idTag, $db) => new DiagnosticsStatusNotificationStationMessage($data));
        $this->registerHandler('FirmwareStatusNotification', fn($data, $idTag, $db) => new FirmwareStatusNotificationStationMessage($data));
    }

    public function create(string $messageType, array $data, ?string $idTag = null, $db = null): StationMessage
    {
        if (!isset($this->messageHandlers[$messageType])) {
            throw new \Exception('Unsupported message type: ' . $messageType);
        }
        // Resolve the appropriate handler
        return call_user_func($this->messageHandlers[$messageType], $data, $idTag, $db);
    }

    public function registerHandler(string $messageType, callable $handler): void
    {
        $this->messageHandlers[$messageType] = $handler;
    }

}




