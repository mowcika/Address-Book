<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employer extends Model
{
    use HasFactory;

    protected $table = 'employer';
    protected $fillable = [
        'company-name',
        'image',
        'type',
        'employer',
        'pincode',
        'company_type',
        'address',
        'city',
        'company-emp-size',
        'district',
        'state',
        'designation',
        'whatsapp'
    ];

    const CREATED_AT = 'indate'; // CREATED_AT to indate
    const UPDATED_AT = 'update'; // CREATED_AT to update
}
