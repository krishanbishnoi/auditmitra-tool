<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productattribute extends Model
{
    use HasFactory;

    protected $table = 'productattributes';

    protected $fillable = ['product_id', 'product_attribute_name','type', 'is_recovery','status', 'client_id'];

    // public function productattr()
    // {
    //     return $this->hasOne('App\Model\Products', 'id','product_id');
    // }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function productName()
    {
        return $this->hasOne('App\Model\Products', 'id','product_id');
    }
}
