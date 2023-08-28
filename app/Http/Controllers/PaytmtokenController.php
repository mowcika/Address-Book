<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AppNameModel;

class PaytmtokenController extends Controller
{
    public function getPaytmToken(Request $request)
    {

        return AppNameModel::where([['is_deleted', 0]])->orderBy('id', 'DESC')->get();
//        return 'pandiya';
    }
}
