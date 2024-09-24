<?php
namespace MyApp\Messages\Station;
use MyApp\MySQL;
use MyApp\Responses\Station\MeterValuesStationResponseInterface;
use MyApp\Messages\Station\Base\StationMessageHandler;

class MeterValuesStationMessage extends StationMessageHandler{

    private string $Sample_message = '[2,"15455","Heartbeat",{}]';

    public function __construct(private array $data, private readonly string $chargerTag, public MySQL $db)
    {
        $transactionId = $this->data[3]['transactionId'];

        $sql_get_user_data = "SELECT customer_start_kwh,extra_data, starting_kwh, slot_type,customer_id FROM slot_charges WHERE transactionId = '$transactionId' ";
        $slot_charge_data = $this->db->query($sql_get_user_data, null,"select");
        $charged_till_now = 0;


        $charge_data = array();
        $current_charge = 0;
        $current_charge_kwh = 0;
        foreach ($this->data[3]['meterValue'][0]['sampledValue'] as $meter_data) {
            if (isset($meter_data['measurand'])) {
                $charge_data[$meter_data['measurand']] = $meter_data['value'] . " " . $meter_data['unit'];
                if ($meter_data['measurand'] === "SoC") $current_charge = intval($meter_data['value']);
                if ($meter_data['measurand'] === "Energy.Active.Import.Register") $current_charge_kwh = intval($meter_data['value']);
                if ($meter_data['measurand'] === "Power.Active.Import") $current_charge_k = intval($meter_data['value']);
                if ($meter_data['measurand'] === "Current.Import") $amper = intval($meter_data['value']);
                if ($meter_data['measurand'] === "Voltage") $volt = intval($meter_data['value']);
            }
        }
        if(!is_null($slot_charge_data[0]['starting_kwh'])){
            $charged_till_now = $current_charge_kwh - $slot_charge_data[0]['starting_kwh'];
        }

        if ($slot_charge_data[0]['slot_type'] != 'CORPORATE') {
            //deccresse kw from customer table
            $sql = "UPDATE customers SET availbe_charges = availbe_charges - $charged_till_now WHERE customer_id = {$slot_charge_data[0]['customer_id']}";
            $this->db->query($sql);
        }
        $sql = "UPDATE slot_charges SET end_kwh = , starting_kwh = IFNULL(starting_kwh,'$current_charge'), current_procent = '$current_charge',  charged_kwh = '$charged_till_now' , startTransactionData = IFNULL(startTransactionData, '" . json_encode($charge_data) . "') ,  extra_data ='" . json_encode($charge_data) . "'  WHERE transactionId = '$transactionId' ";
        $this->db->query($sql);
        //auto close if customer alloved kwh <=1


        $send_end_message = false;
        if(isset($slot_charge_data[0]['customer_start_kwh'])) {
            $kwh_left = $slot_charge_data[0]['customer_start_kwh'] - $charged_till_now;
            if($slot_charge_data[0]['slot_type'] == 'BOOST'){
                if ($current_charge >= intval($slot_charge_data[0]['extra_data'])) {
                    $send_end_message = true;
                }
            }elseif ($slot_charge_data[0]['slot_type'] == 'CORPORATE'){
                if ($current_charge >= 90) {
                    $send_end_message = true;
                }
            }else{
                if ($current_charge >= 90 || $kwh_left <=1) {
                    $send_end_message = true;
                }
            }
        }else{
            $send_end_message = true;
        }
        if($send_end_message){
            $tmp_id = gmdate("U");
            //trigger close transaction
            $sql = "INSERT INTO ocpp_messages_to_send (charger_idTag,message) values ('$chargerTag','[2,\"$tmp_id\",\"RemoteStopTransaction\",{\"transactionId\":$transactionId}]');";
            $this->db->query($sql);
        }
        $this->setResponse(new MeterValuesStationResponseInterface($this->data));

    }

}


