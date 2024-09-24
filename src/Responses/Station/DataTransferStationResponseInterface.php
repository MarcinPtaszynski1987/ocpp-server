<?php
namespace MyApp\Responses\Station;
use MyApp\Responses\Station\Base\StationResponse;
use stdClass;

class DataTransferStationResponseInterface extends StationResponse {

    private string $Sample_message = '[2,"01221201194032","Authorize",{ "vendorId":"VendorX", "messageId":"UpdateFirmware","data":"FirmwareVersion1.2.3"}]';

    public function __construct(private readonly array $data)
    {
        $tmp = new stdClass();
        $idTagInfo= new stdClass();
        $idTagInfo->data = "Update initiated";
        $idTagInfo->status = "Accepted";
        $tmp->idTagInfo =$idTagInfo;
        $this->setMessage($tmp, $this->data[1]);
    }

}