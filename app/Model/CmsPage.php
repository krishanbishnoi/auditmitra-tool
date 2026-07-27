<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    use HasFactory;

    // Table name (optional if table name is 'cms_pages')
    protected $table = 'cms_pages';

    // Fields allowed for mass assignment
    protected $fillable = [
        'name',
        'title',
        'slug',
        'content',
        'file_path',
        'status',
    ];

    // If you want to cast or add any attribute, you can add here
    // For example, cast 'status' to boolean
    protected $casts = [
        'status' => 'boolean',
    ];
}
