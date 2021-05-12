<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\Income;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'income_id', 'name', 'amount', 'created_at', 'updated_at'];

    protected $appends = ['amount_str', 'time'];

    public function getAmountStrAttribute() {
        return number_format($this->amount);
    }

    public function getTimeAttribute() {
        return date("h:i A", strtotime($this->created_at));
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function income() {
        return $this->belongsTo(Income::class);
    }
}
