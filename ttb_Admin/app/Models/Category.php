<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';


    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'created_at'
    ];


    public $timestamps = false;


    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime'
    ];
}