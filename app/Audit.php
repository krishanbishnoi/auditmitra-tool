<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    public $timestamps = true;
    protected $fillable = ['present_auditor', 'qm_sheet_id', 'audit_date_by_aud', 'audit_cycle_id', 'parent_branch_id', 'audited_by_id', 'is_critical', 'overall_score', 'branch_id', 'product_id', 'yard_id', 'agency_id', 'collection_manager_email', 'agency_manager_email', 'yard_manager_email', 'collection_manager_id', 'grade', 'score_percentage', 'client_id', 'status', 'last_updated_by'];
    public function raw_data()
    {
        return $this->belongsTo('App\RawData');
    }
    public function qmsheet()
    {
        return $this->belongsTo('App\QmSheet', 'qm_sheet_id', 'id');
    }
    public function audit_cycle()
    {
        return $this->belongsTo('App\AuditCycle', 'audit_cycle_id', 'id');
    }
    public function product()
    {
        return $this->belongsTo('App\Model\Products', 'product_id', 'id');
    }
    public function productnew()
    {
        return $this->hasOne('App\Model\Products', 'id', 'product_id');
    }
    public function branch()
    {
        return $this->belongsTo('App\Model\Branch', 'branch_id', 'id');
    }
    public function branchnew()
    {
        return $this->belongsTo('App\Model\Branch', 'parent_branch_id', 'id');
    }
    public function yard()
    {
        return $this->belongsTo('App\Yard', 'yard_id', 'id');
    }
    public function qc()
    {
        return $this->hasOne('App\Qc', 'audit_id', 'id');
    }
    public function agency()
    {
        return $this->belongsTo('App\Agency', 'agency_id', 'id');
    }
    public function branchRepo()
    {
        return $this->belongsTo('App\Model\BranchRepo', 'branch_repo_id', 'id');
    }
    public function agencyRepo()
    {
        return $this->belongsTo('App\Model\AgencyRepo', 'agency_repo_id', 'id');
    }
    public function audit_parameter_result()
    {
        return $this->hasMany('App\AuditParameterResult');
    }
    public function audit_results()
    {
        return $this->hasMany('App\AuditResult', 'audit_id');
    }
    public function audit_reports()
    {
        return $this->hasOne('App\AuditReport', 'audit_id');
    }
    public function qa_qtl_detail()
    {
        return $this->belongsTo('App\User', 'audited_by_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo('App\User', 'collection_manager_id', 'id');
    }
    public function audit_rebuttal()
    {
        return $this->hasMany('App\Rebuttal', 'audit_id', 'id');
    }
    public function audit_rebuttal_accepted()
    {
        return $this->hasMany('App\Rebuttal', 'audit_id')->where('status', 1);
    }
    public function audit_rebuttal_rejected()
    {
        return $this->hasMany('App\Rebuttal', 'audit_id')->where('status', 2);
    }
    public function agencyUser()
    {
        return $this->belongsTo('App\User', 'agency_manager_email', 'email');
    }
    public function yardUser()
    {
        return $this->belongsTo('App\User', 'yard_manager_email', 'email');
    }
    public function collectionUser()
    {
        return $this->belongsTo('App\User', 'collection_manager_email', 'email');
    }
    public function collectionManagerData()
    {
        return $this->hasOne('App\User', 'id', 'collection_manager_id');
    }
    public function redAlert()
    {
        return $this->hasMany('App\RedAlert', 'audit_id', 'id');
    }
    public function artifact()
    {
        return $this->hasMany('App\Artifact', 'audit_id', 'id');
    }
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->setTimezone('Asia/Kolkata');
    }
}
