<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reading extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'meterman_id',
        'prev_reading',
        'current_reading',
        'cum_price',
        'price',
    ];

    protected $with = ['client'];

    protected $appends = [
        'date_metered',
    ];

    public function getDateMeteredAttribute()
    {
        $due = Carbon::parse($this->created_at);
        return $due->format('M-d-Y');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function meterman()
    {
        return $this->belongsTo(User::class, 'meterman_id');
    }
}
