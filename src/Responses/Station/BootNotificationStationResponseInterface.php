<?php
namespace MyApp\Responses\Station;
use DateTime;
use DateTimeZone;
use MyApp\Responses\Station\Base\StationResponse;
use stdClass;

class BootNotificationStationResponseInterface extends StationResponse {

    private string $Sample_message = '[2,"15455","BootNotification",{"chargePointVendor":"vekon","chargePointModel":"","chargePointSerialNumber":"","chargeBoxSerialNumber":"","firmwareVersion":"","meterType":""}]';

    /**
     * @throws \Exception
     */
    public function __construct(private readonly array $data)
    {
        $dateTime = new DateTime('now', new DateTimeZone('UTC'));
        $formattedDate = $dateTime->format('Y-m-d\TH:i:s\Z');
        $tmp = new stdClass();
        $tmp->currentTime =$formattedDate;
        $tmp->interval =360;
        $tmp->status ="Accepted";
        $this->setMessage($tmp ,$this->data[1]);
    }

}