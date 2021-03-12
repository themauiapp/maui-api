<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Period;

class Income extends Model
{
    use HasFactory;

    protected $table = 'income';

    protected $fillable = ['user_id', 'period_id', 'total' , 'remainder'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function period() {
        return $this->belongsTo(Period::class);
    }
}
