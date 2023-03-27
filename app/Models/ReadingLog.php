<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadingLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = [
        'changer',
        'client',
    ];

    public function changer()
    {
        return $this->belongsTo(User::class, 'changer_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
