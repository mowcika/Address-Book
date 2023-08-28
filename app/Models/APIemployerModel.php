<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Str;

class APIemployerModel extends Model
{
    protected $table = 'personal_access_tokens';
    protected $primaryKey = 'id';
    use HasApiTokens, HasFactory, Notifiable;
}
