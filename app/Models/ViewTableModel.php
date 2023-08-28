<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewTableModel extends Model
{
    public $timestamps = false;
    protected $table = 'view_table';
    use HasFactory;
}
