<?php
namespace MyApp\Messages\Server\Base;
interface ServerMessage {
    public function parse(): array;
    public function getSampleMesssage(): string;
}