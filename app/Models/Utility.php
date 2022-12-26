<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Utility extends Model
{
    use HasFactory;

    protected $fillable = [
        'field',
        'value',
    ];

    protected $appends = [
        'date_created',
    ];

    public function getDateCreatedAttribute()
    {
        return date("d-M-Y", strtotime($this->created_at));
    }
}
