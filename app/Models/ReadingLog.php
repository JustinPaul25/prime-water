<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadingLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = [
        'changer',
        'client',
    ];

    protected $appends = [
        'date_created',
    ];

    public function getDateCreatedAttribute()
    {
        return $this->created_at->format('M-d-Y');
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'changer_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
