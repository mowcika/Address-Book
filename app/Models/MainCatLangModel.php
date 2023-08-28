<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainCatLangModel extends Model
{
    public $timestamps = false;
    protected $table = 'main_cat_lang_map';
    use HasFactory;
}
