<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessPermission extends Model
{
    use HasFactory;

    protected $table = 'accessPermission';
    protected $fillable = ['id', 'role_id', 'menu_id', 'cdate', 'cby', 'cip', 'mby', 'mdate', 'mip'];
    public $timestamps = false;
}
