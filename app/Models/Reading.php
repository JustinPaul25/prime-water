<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reading extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'meterman_id',
        'prev_reading',
        'current_reading',
        'price',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function meterman()
    {
        return $this->belongsTo(User::class, 'meterman_id');
    }
}
