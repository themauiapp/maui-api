<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UniqueExpense extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'total'];
}
