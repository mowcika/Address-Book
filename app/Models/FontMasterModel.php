<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FontMasterModel extends Model
{
    protected $table = 'font_size_master';
    const CREATED_AT = 'cdate'; // CREATED_AT to indate
    const UPDATED_AT = 'mdate';
    use HasFactory;
}
