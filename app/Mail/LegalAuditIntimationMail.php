<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LegalAuditIntimationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $auditorName;
    public $auditDate;
    public $auditAssignId;

    public function __construct($auditorName, $auditDate, $auditAssignId)
    {
        $this->auditorName   = $auditorName;
        $this->auditDate     = $auditDate;
        $this->auditAssignId = $auditAssignId;
    }

    public function build()
    {
        return $this->subject('Legal Audit Intimation')
            ->view('legal.emails.legal_audit_intimation');
    }
}
