<?php

namespace MyApp\Parsers;

use MyApp\Messages\Station\Base\StationMessage;
use MyApp\MySQL;

class StationMessageParser {

    private StationMessage $messageHandler;
    private array $data;
    private string $idTag;
    public ?MySQL $db;

    /**
     * @throws \Exception
     */
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
        $swith_by = $this->data[2] ?? $this->data['action'];
        $factory = new StationMessageFactory();
        $this->messageHandler = $factory->create($swith_by, $this->data, $this->idTag, $this->db);
    }
    public function parse(): array{
       return $this->messageHandler->parse();
    }
}