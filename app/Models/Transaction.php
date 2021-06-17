<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function payer() {
        return $this->belongsTo(User::class, 'id');
    }

    public function receiver() {
        return $this->hasOne(User::class, 'id', 'receiver_id');
    }
}
