<?php
namespace MyApp\Messages\Station\Base;

use MyApp\Responses\Station\Base\StationResponse;

abstract class StationMessageHandler implements StationMessage{
    private string $sampleMessage;
    private StationResponse $response;

    public function setResponse(StationResponse $response){
        $this->response = $response;
    }
    public function getSampleMesssage(): string{
        return $this->sampleMessage;
    }
    public function parse(): array
    {
        $return = array();
        $return['status'] = "Success";
        $return['response'] =$this->response->getAsString();
        return $return;
    }

}