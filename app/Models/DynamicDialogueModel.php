<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicDialogueModel extends Model
{
    protected $table = 'dynamic_dialogue';
    const CREATED_AT = 'cdate'; // CREATED_AT to indate
    const UPDATED_AT = 'mdate';
    use HasFactory;
}
