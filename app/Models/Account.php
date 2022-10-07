<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'prev_reading',
        'current_reading',
        'prev_balance',
        'current_charges',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
