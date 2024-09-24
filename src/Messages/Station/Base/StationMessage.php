<?php
namespace MyApp\Messages\Station\Base;
interface StationMessage {
    public function parse(): array;
    public function getSampleMesssage(): string;
}