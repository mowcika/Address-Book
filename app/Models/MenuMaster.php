<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuMaster extends Model
{
    use HasFactory;

    protected $table = 'gcmpro_menu';
    protected $fillable = ['id', 'group_name', 'menus', 'deleted', 'main_menu', 'sub_menu', 'route', 'icon', 'parent', 'position'];
    public $timestamps = false;
}
