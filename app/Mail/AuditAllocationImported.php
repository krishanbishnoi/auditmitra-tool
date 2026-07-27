<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\Model\AuditAllocation;

class AuditAllocationImported extends Mailable
{
    use Queueable, SerializesModels;

    public $userId;
    public $agencyEmail;
    public $auditAllocations; //V All Data

    public function __construct($userId, $agencyEmail, $auditAllocations)
    {
        $this->userId = $userId;
        $this->agencyEmail = $agencyEmail;
        $this->auditAllocations = $auditAllocations; // Store the data
    }

    public function build()
    {
        return $this->subject('Allocation Mail')
                    ->view('emails.audit_allocation_imported')
                    ->with([
                        'userId' => $this->userId,
                        'agencyEmail' => $this->agencyEmail,
                        'auditAllocations' => $this->auditAllocations,
                    ]);
    }
}

