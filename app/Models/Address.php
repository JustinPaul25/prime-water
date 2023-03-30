<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['address'];

    public function getAddressAttribute()
    {
        return 'Prk - '.$this->prk.', Consolacion, Panabo City, Davao Del Norte';
    }
}
