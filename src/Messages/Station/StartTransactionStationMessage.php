<?php
namespace MyApp\Messages\Station;
use MyApp\Messages\Station\Base\StationMessageHandler;
use MyApp\MySQL;
use MyApp\Responses\Station\StartTransactionStationResponse;

class StartTransactionStationMessage  extends StationMessageHandler{

    private string $Sample_message = '[2,"15455","StartTransaction",{"connectorId":1,"idTag":" idTag_Charger","meterStart":0,"timestamp":"2022-11-21T14:55:45.341Z"}]';

    public function __construct(private readonly array $data, private readonly string $stationId,public MySQL $db)
    {
        $transactionId = date("U");
        $sql = "UPDATE ocpp_charges_tags SET transaction_id = '$transactionId', last_action = 'StartTransaction'  WHERE bakend_tag = '{$this->stationId}' ";
        $this->db->query($sql);
        $sql = "UPDATE slot_charges SET transactionId = '$transactionId'  WHERE chargerTag = '{$this->stationId}' AND status = 'IN_PROGRESS' ";
        $this->db->query($sql);

        $this->setResponse(new StartTransactionStationResponse($this->data, $this->stationId, $transactionId));
    }
}