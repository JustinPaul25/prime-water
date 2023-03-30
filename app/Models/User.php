<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasRoles;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'contact_no',
        'address_id',
        'status',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['role', 'account', 'address'];

    public function getRoleAttribute()
    {
        $roles = $this->getRoleNames();

        return $roles[0];
    }

    public function getAccountAttribute()
    {
        return Account::where('client_id', $this->id)->first();
    }

    public function getAddressAttribute()
    {
        $address = Address::where('id', $this->address_id)->first();
        return $address ? 'Prk - '.$address->prk.', Consolacion, Panabo City, Davao Del Norte' : null;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('Admin');
    }

    public function isClient(): bool
    {
        return $this->hasRole('Client');
    }

    public function isMeterman(): bool
    {
        return $this->hasRole('Meterman');
    }

    public function isCashier(): bool
    {
        return $this->hasRole('Cashier');
    }

    public function account()
    {
        return $this->hasOne(Account::class, 'client_id', 'id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
