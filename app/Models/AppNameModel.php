<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppNameModel extends Model
{
    protected $table = 'app_master';
    const CREATED_AT = 'cdate'; // CREATED_AT to indate
    const UPDATED_AT = 'mdate';
    use HasFactory;
}
