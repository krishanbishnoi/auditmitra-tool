<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class OpenPointersWithSummaryExport implements WithMultipleSheets
{
    protected $startDate;
    protected $endDate;
    protected $clientId;

    public function __construct($startDate, $endDate, $clientId)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
        $this->clientId  = $clientId;
    }

    public function sheets(): array
    {
        return [
            // Sheet 1 (Existing – DO NOT TOUCH)
            new OpenPointersDump($this->startDate, $this->endDate),

            // Sheet 2 (New Summary)
            new AgencyOpenClosedSummarySheet(
                $this->startDate,
                $this->endDate,
                $this->clientId
            ),
        ];
    }
}
