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
    public static function create(string $switch_by, array $data, ?string $idTag, $db): StationMessage
    {
        return match ($switch_by) {
            "Authorize" => new AuthorizeStationMessage($data[1], $db),
            "BootNotification" => new BootNotificationStationMessage($data),
            "ChangeAvailability" => new ChangeAvailabilityStationMessage($data),
            "DataTransfer" => new DataTransferStationMessage($data),
            "Heartbeat" => new HeartbeatStationMessage($data[1]),
            "MeterValues" => new MeterValuesStationMessage($data, $idTag, $db),
            "StartTransaction" => new StartTransactionStationMessage($data, $idTag, $db),
            "StatusNotification" => new StatusNotificationStationMessage($data, $idTag),
            "StopTransaction" => new StopTransactionStationMessage($data, $idTag, $db),
            "DiagnosticsStatusNotification" => new DiagnosticsStatusNotificationStationMessage($data),
            "FirmwareStatusNotification" => new FirmwareStatusNotificationStationMessage($data),
            default => throw new \Exception('Unexpected message type'),
        };
    }
}