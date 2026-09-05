<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ClientMasterSetting extends Model
{
  protected $table = 'client_master_settings';

  protected $fillable = [
    'field_name',
    'field_value',
    'client_id',
  ];
}
