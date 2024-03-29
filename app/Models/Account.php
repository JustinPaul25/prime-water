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
        'is_metered',
        'current_date_reading',
        'prev_date_reading',
    ];

    public function getDueDateAttribute()
    {
        $last_reading = Reading::where('client_id', $this->client_id)->latest()->first();
        if($last_reading) {
            $due = Carbon::parse($last_reading->created_at)->addDays(30);
            return $due->format('M-d-Y');
        } else {
            return null;
        }
    }

    public function getIsMeteredAttribute() {
        $reading = Reading::where('client_id', $this->client_id)->latest()->first();
        if($reading) {
            $readMonthYear = $reading->created_at->month . '-' . $reading->created_at->year;
            $nowMonthYear = now()->month . '-' . now()->year;

            return $readMonthYear === $nowMonthYear;
        } else {
            return;
        }
    }

    public function getCurrentDateReadingAttribute()
    {
        $reading = Reading::where('client_id', $this->client_id)->latest()->first();
        return $reading ? $reading->created_at->format('M-d-Y') : null;
    }

    public function getMonthLastPaymentAttribute()
    {
        if($this->last_payment) {
            $date1 = explode (" ", $this->last_payment);
            $date1 = $date1[0];
        } else {
            $date1 = $this->created_at->format('M-d-Y');
        }

        $date2 = date('M-d-Y');;

        $ts1 = strtotime($date1);
        $ts2 = strtotime($date2);

        $year1 = date('Y', $ts1);
        $year2 = date('Y', $ts2);

        $month1 = date('m', $ts1);
        $month2 = date('m', $ts2);

        $diff = (($year2 - $year1) * 12) + ($month2 - $month1);

        return $diff;
    }

    public function getPrevDateReadingAttribute()
    {
        $reading = Reading::where('client_id', $this->client_id)->orderBy('created_at', 'desc')->skip(1)->take(1)->first();

        if($reading) {
            return $reading->created_at->format('M-d-Y');
        } else {
            return null;
        }
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
