<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'prev_reading',
        'current_reading',
        'prev_balance',
        'current_charges',
        'last_payment',
    ];

    protected $appends = [
        'month_last_payment',
        'due_date',
    ];

    public function getDueDateAttribute()
    {
        $due = Carbon::parse($this->last_payment)->addMonth(1);
        return $due->format('M-d-Y');
    }

    public function getMonthLastPaymentAttribute()
    {
        if($this->last_payment) {
            $date1 = explode (" ", $this->last_payment);
            $date1 = $date1[0];
        } else {
            $date1 = $this->created_at->format('Y-m-d');
        }

        $date2 = date('Y-m-d');;

        $ts1 = strtotime($date1);
        $ts2 = strtotime($date2);

        $year1 = date('Y', $ts1);
        $year2 = date('Y', $ts2);

        $month1 = date('m', $ts1);
        $month2 = date('m', $ts2);

        $diff = (($year2 - $year1) * 12) + ($month2 - $month1);

        return $diff;
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
