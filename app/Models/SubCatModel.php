<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCatModel extends Model
{
    public $timestamps = false;
    protected $table = 'sub_cat';
    use HasFactory;
}
