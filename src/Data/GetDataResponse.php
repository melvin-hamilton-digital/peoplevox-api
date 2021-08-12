<?php

namespace MHD\Peoplevox\Data;

class GetDataResponse
{
    /**
     * @var string
     */
    public $csvData;
    /**
     * @var int
     */
    public $totalCount;

    public function __construct(string $csvData, int $totalCount)
    {
        $this->csvData = $csvData;
        $this->totalCount = $totalCount;
    }
}
