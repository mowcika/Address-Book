<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\AddressModel;
use Illuminate\Support\Facades\DB;

class MobileApi extends Controller
{
    public function dynamicServerJson(Request $request)
    {
        $indate = Carbon::now()->toDateTimeString();
        $inip = $request->ip();
        $action = $request->action;

        if ($action == 'getAddress') {
            $output = DB::table('address')
                ->selectRaw("id,name,address,dob,mobile1,mobile2,email,wnumber")
                ->where('is_delete', '=', '0')
                ->orderBy('id', 'ASC')->get();
        }
        return $output;
    }
}
