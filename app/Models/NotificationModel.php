<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationModel extends Model
{
    protected $table = 'save_notification';
    const CREATED_AT = 'cdate'; // CREATED_AT to indate
    const UPDATED_AT = 'mdate';
    use HasFactory;
}
