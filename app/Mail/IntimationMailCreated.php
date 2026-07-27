<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Model\IntimationMail;
use App\Helpers\Helper;
use DB;

class IntimationMailCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $intimationMail;

    /**
     * Create a new message instance.
     *
     * @param IntimationMail $intimationMail
     * @return void
     */
    public function __construct(IntimationMail $intimationMail)
    {
        $this->intimationMail = $intimationMail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $allocatedmodule = Helper::allocatedmodulelist();

        // dd($this->intimationMail);
        // Dynamic subject based on process_review_month
        $subject = 'Process Review Schedule for ' . $this->intimationMail->process_review_month;
        if ($this->intimationMail->client_id == 74) {
            $subject = strtoupper('FIBE_AUDIT_' . $this->intimationMail->mode . '_' . $this->intimationMail->name . '_' . $this->intimationMail->location);
        }
        if ($this->intimationMail->client_id == 15) {
            $subject = strtoupper('Whizdm Finance (Moneyview) Internal Audit(' . $this->intimationMail->name . ')');
        }
        if ($this->intimationMail->client_id == 285) {
            $subject = strtoupper('Assessment Schedule Confirmation - ' . $this->intimationMail->name . ' | Request for Confirmation and Support');
        }
        $agency = DB::table('agencies')->where('id', $this->intimationMail->agency)->first();
        $state = DB::table('states')->where('id', $agency->state)->value('name');
        $zone = DB::table('regions')->where('id', $agency->region_id)->value('name');
        // $view = in_array(21, $allocatedmodule)
        // ? 'emails.intimation_mail_moneyview'   // Custom view for module 31
        // : 'emails.intimation_mail_created';  // Default view
        // dd(auth()->user()->client_id);
        if (auth()->user()->client_id == 15) {
            $view = 'emails.intimation_mail_moneyview';
        } elseif (auth()->user()->client_id == 74) {
            $view = 'emails.intimation_mail_fibe';
        } elseif (auth()->user()->client_id == 249) {
            $view = 'emails.intimation_mail_creditsaison';
        } elseif (auth()->user()->client_id == 285) {
            $view = 'emails.intimation_mail_poonawalla';
        } else {
            $view = 'emails.intimation_mail_created';
        }
        $viewData = [
            'name' => $this->intimationMail->name,
            'audit_date' => $this->intimationMail->audit_date,
            'agency' => $agency,
            'agency_email' => $this->intimationMail->agency_email,
            'process_review_month' => $this->intimationMail->process_review_month,
            'description' => $this->intimationMail->description,
            'mode' => $this->intimationMail->mode ?? null,
            'auditor_name' => $this->intimationMail->auditor,
            'state' => $state,
            'zone' => $zone,
            'client_spoc' => $this->intimationMail->client_spoc,
            'agency_spoc' => $this->intimationMail->agency_spoc,
            'agency_spoc_number' => $this->intimationMail->agency_spoc_number,
        ];

        if (!empty($this->intimationMail->auditor_name)) {
            $viewData['auditor_name_real'] = $this->intimationMail->auditor_name;
        }

        if (!empty($this->intimationMail->executives)) {
            $viewData['executives'] = json_decode($this->intimationMail->executives, true) ?? [];
        }

        $email = $this->view($view)
            ->subject($subject)
            ->with($viewData);


        if ($this->intimationMail->client_id == 74) {

            // Attach PDF
            $pdfPath = public_path('Fibe Audit Checksheet.pdf');
            if (file_exists($pdfPath)) {
                $email->attach($pdfPath, [
                    'as' => 'FIBE_AGENCY_AUDIT_CHECKSHEET.pdf',
                    'mime' => 'application/pdf',
                ]);
            }

            // Attach Excel File
            $excelPath = public_path('Format of Agent Details tracking sheet.xlsx');
            if (file_exists($excelPath)) {
                $email->attach($excelPath, [
                    'as' => 'Format_Of_Agent_Details_Tracking_Sheet.xlsx',
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]);
            }
        }
        if ($this->intimationMail->client_id == 15) {
            $pdfPath = public_path('Moneyview_Audit Checksheet.pdf');
            if (file_exists($pdfPath)) {
                $email->attach($pdfPath, [
                    'as' => 'MONEYVIEW_AUDIT_CHECKSHEET.pdf',
                    'mime' => 'application/pdf',
                ]);
            }
        }

        if ($this->intimationMail->client_id == 249) {
            $pdfPath = public_path('Credit Saison Checklist.xlsx');
            if (file_exists($pdfPath)) {
                $email->attach($pdfPath, [
                    'as' => 'CREDITSAISON_AUDIT_CHECKSHEET.xlsx',
                    'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]);
            }
        }

        return $email;
    }
}
