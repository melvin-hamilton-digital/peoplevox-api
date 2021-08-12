<?php

namespace MHD\Peoplevox\Data;

use stdClass;

class Session extends stdClass
{
    /**
     * @var int
     */
    public $UserId;
    /**
     * @var string
     */
    public $ClientId;
    /**
     * @var string
     */
    public $SessionId;

    public function __construct(string $clientId, string $sessionId, int $userId = 0)
    {
        $this->ClientId = $clientId;
        $this->SessionId = $sessionId;
        $this->UserId = $userId;
    }
}
