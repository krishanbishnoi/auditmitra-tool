<?php

namespace App\Imports;

use App\SupportTicket;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportSupportTicketSheet implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function model(array $row)
    {

        if (empty($row['help_topic'])) {
            return null;
        }
             $supportTicket = SupportTicket::create([
            'support_id' => $row['support_id'],
            'help_topic' => $row['help_topic'] ?? null,
            'issue_type' => $row['issue_type'] ?? null,
            'subject' => $row['subject'] ?? null,
            'priority' => $row['priority'] ?? null,
            'description' => $row['description'] ?? null,
            'status' => $row['status'] ?? null,
            'closure_feedback' => $row['closure_feedback'] ?? null,
            'created_at' => $row['created_at'] ?? null
        ]);

        return $supportTicket;
    }
}
