<?php
namespace MyApp\Responses\Station\Base;
abstract class StationResponse {
    private mixed $message;
    public function setMessage(mixed $message, string $idTag): void {
        $tmp = array(3,"$idTag", $message);
        $this->message = $tmp;
    }

    public function getAsString(): string
    {
        if(is_string($this->message)){
            return $this->message;
        }elseif (is_array($this->message)){
            return json_encode($this->message);
        }else{
            return "";
        }

    }
}