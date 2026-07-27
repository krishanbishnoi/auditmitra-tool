<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class IntimationMail extends Model
{
    protected $table = 'intimation_mail';

    protected $fillable = [
        'user_id',
        'name',
        'process_review_month',
        'audit_date',
        'agency',  // This is assuming that the column name is 'agency' and not 'agency_id'
        'email',
        'product_id',
        'product_attribute_id',
        'collection_manager',
        'auditor',
        'description',
        'lavel_3',
        'lavel_4',
        'lavel_5',
        'client_id',
    ];

    /**
     * Relationship with Agency model
     */
    public function agency()
    {
        return $this->belongsTo('App\Agency', 'agency');
    }

    /**
     * Relationship with Product model
     */
    public function product()
    {
        return $this->belongsTo('App\Model\Products', 'product_id');
    }

    /**
     * Relationship with Product Attribute model
     */
    public function productAttribute()
    {
        return $this->belongsTo('App\Model\Productattribute', 'product_attribute_id');
    }

    /**
     * Relationship with User model (creator of the intimation)
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * Relationship with User model (auditor)
     */
    public function auditorUser()
    {
        return $this->belongsTo('App\User', 'auditor', 'id');  // Assuming auditor is a user_id
    }

    /**
     * Fetch Level 3 Users (assuming lavel_3 is comma-separated)
     */
    public function level3Users()
    {
        return User::whereIn('id', explode(',', $this->lavel_3))->get();
    }

    /**
     * Fetch Level 4 Users (assuming lavel_4 is comma-separated)
     */
    public function level4Users()
    {
        return User::whereIn('id', explode(',', $this->lavel_4))->get();
    }

    /**
     * Fetch Level 5 Users (assuming lavel_5 is comma-separated)
     */
    public function level5Users()
    {
        return User::whereIn('id', explode(',', $this->lavel_5))->get();
    }
}
