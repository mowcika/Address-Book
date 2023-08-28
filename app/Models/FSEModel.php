<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FSEModel extends Model
{
    protected $table = 'fseDetails';
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employer_id',
        'type',
        'contactPersonName',
        'contactPersonMobile',
        'contactPersonDesignation',
        'companyName',
        'companyRegisteredMobile',
        'companyAddress',
        'companyGeoAddress',
        'companyGeoLat',
        'companyGeoLang',
        'lastFollowStatus',
        'lastFollowDate',
        'nextFollowDate',
        'lastFollowRemarks',
        'lastFollowBy',
        'imageField'
    ];

    public $timestamps = false;
}
