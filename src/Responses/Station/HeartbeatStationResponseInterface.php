<?php
namespace MyApp\Responses\Station;

use DateTime;
use DateTimeZone;
use MyApp\Responses\Station\Base\StationResponseInterface    ;
use MyApp\Responses\Station\Base\StationResponse;
use stdClass;

class HeartbeatStationResponseInterface extends StationResponse  implements StationResponseInterface {

    private string $Sample_message = '[2,"01221201194032","Authorize",{"idTag":"D0431F35"}]';

    public function __construct(private readonly string $idTag = "")
    {
        $dateTime = new DateTime('now', new DateTimeZone('UTC'));
        $formattedDate = $dateTime->format('Y-m-d\TH:i:s\Z');
        $tmp = new stdClass();
        $tmp->currentTime = $formattedDate;
        $this->setMessage($tmp, $this->idTag);
    }

}