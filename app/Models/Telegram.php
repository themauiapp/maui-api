<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Telegram extends Model
{
    use HasFactory;

    protected $table = 'telegram_settings';

    protected $fillable = ['telegram_id', 'notify_12pm', 'notify_6pm', 'notify_10pm'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
