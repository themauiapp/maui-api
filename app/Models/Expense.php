<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Income;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'income_id', 'name', 'amount'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function income() {
        return $this->belongsTo(Income::class);
    }
}
