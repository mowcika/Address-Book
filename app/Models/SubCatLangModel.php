<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCatLangModel extends Model
{
    public $timestamps = false;
    protected $table = 'sub_cat_lang_map';
    use HasFactory;
}
