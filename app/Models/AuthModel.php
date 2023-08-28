<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class AuthModel extends Authenticatable
{
    protected $table = 'employer_registration';
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'phonenumber',
        'otp',
        'firstname',
        'designation',
        'company_details',
    ];
//    public $timestamps = false; // this not update CREATED_AT, UPDATED_AT
    const CREATED_AT = 'indate'; // CREATED_AT to indate
    const UPDATED_AT = 'update'; // CREATED_AT to update
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
}
