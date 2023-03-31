<?php

namespace App\Models;

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
        $user = User::find($this->client_id);

        if(!$user) {
            $user =  User::onlyTrashed()->find($this->client_id);
        }

        return $user;
    }

    public function getFormatCreatedAtAttribute()
    {
        return $this->created_at->format('M-d-Y');
    }

    public function getDatePaidAttribute()
    {
        return date("M-d-Y", strtotime($this->created_at));
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
