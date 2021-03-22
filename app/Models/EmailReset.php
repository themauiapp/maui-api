<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailReset extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'token', 'expires_at'];

    public $timestamps = false;

}
