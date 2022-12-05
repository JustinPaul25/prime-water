<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'cashier_id',
        'amount',
    ];

    protected $appends = ['client', 'date_paid', 'format_created_at'];

    public function getClientAttribute()
    {
        $client = User::find($this->client_id);

        return $client;
    }

    public function getFormatCreatedAtAttribute()
    {
        return $this->created_at;
    }

    public function getDatePaidAttribute()
    {
        return date("d-m-Y", strtotime($this->created_at));;
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }
}
