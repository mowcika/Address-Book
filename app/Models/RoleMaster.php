<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleMaster extends Model
{
    use HasFactory;

    protected $table = 'role_master';
    protected $fillable = ['id', 'role', 'isActive', 'cdate', 'cby', 'cip', 'mby', 'mdate', 'mip'];
    public $timestamps = false;

}
