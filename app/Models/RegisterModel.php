<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterModel extends Model
{
const CREATED_AT = 'cdate';
        const UPDATED_AT = 'mdate'; // CREATED_AT to indate
    protected $table = 'register';
    use HasFactory;
}
