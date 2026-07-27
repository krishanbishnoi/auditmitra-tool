<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Model\AuditorAssign;

class AuditorAssignForAudit extends Mailable
{
    use Queueable, SerializesModels;

    public $userId;
    public $agencyEmail;
    public $auditorAssigns;

    public function __construct($userId, $agencyEmail, $auditorAssigns)
    {
        $this->userId = $userId;
        $this->agencyEmail = $agencyEmail;
        $this->auditorAssigns = $auditorAssigns;
    }

    public function build()
    {
        return $this->subject('Audit Assign Mail')
                    ->view('emails.auditor_assign_for_audit')
                    ->with([
                        'userId' => $this->userId,
                        'agencyEmail' => $this->agencyEmail,
                        'auditorAssigns' => $this->auditorAssigns,
                    ]);
    }
}
