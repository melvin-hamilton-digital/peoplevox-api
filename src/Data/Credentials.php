<?php

namespace MHD\Peoplevox\Data;

/**
 * @link https://peoplevox.github.io/Documentation/#21-api-calls
 */
class Credentials
{
    /**
     * @var string
     */
    public $clientId;
    /**
     * @var string
     */
    public $username;
    /**
     * @var string
     */
    public $password;

    public function __construct(
        string $clientId,
        string $username,
        string $password
    ) {
        $this->clientId = $clientId;
        $this->username = $username;
        $this->password = $password;
    }
}
