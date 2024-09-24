<?php
namespace MyApp\Messages\Station;
use Exception;
use MyApp\Messages\Station\Base\StationMessageHandler;
use MyApp\MySQL;
use MyApp\Responses\Station\StopTransactionStationResponseInterface;

class StopTransactionStationMessage extends StationMessageHandler{

    private string $Sample_message = '[2,"15455","Heartbeat",{}]';

    public function __construct(private readonly array $data = array(),private readonly string $stationId,public MySQL $db)
    {
        $reason = "";
        if(isset($this->data[3]['reason'])) $reason = $this->data[3]['reason'];

        $sql = "UPDATE ocpp_charges_tags SET transaction_id = '', last_action = 'StopTransaction $reason'  WHERE bakend_tag = '{$this->stationId}' ";
        $this->db->query($sql);
        try {
            if(isset($this->data[3]['transactionData'])){
                $transactionId = $this->data[3]['transactionId'];
                $charge_data = array();
                $charge_data['stopDate'] = $this->data[3]['transactionData'][0]['timestamp'];
                foreach ($this->data[3]['transactionData'][0]['sampledValue'] as $meter_data){
                    $charge_data[$meter_data['measurand']] = $meter_data['value']." ".$meter_data['unit'];
                }
                $time = gmdate("Y-m-d H:i:s");
                end_kwh
                $sql = "UPDATE slot_charges SET status='DONE', stop_date = '$time' , stopTransactionData ='" . json_encode($charge_data) . "'  WHERE transactionId = '$transactionId' ";
                $this->db->query($sql);
            }
        }catch (Exception $exception){
            echo $exception->getMessage();
        }
        //send driver update schedule status // boost request to stop charger
        $this->setResponse(new StopTransactionStationResponseInterface($this->data, $this->stationId));
    }
}