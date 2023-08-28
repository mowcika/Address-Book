<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jobtitle;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class JobtitleController extends Controller
{
    public function searchJobTitle(Request $request)
    {
//        \DB::enableQueryLog();
//        return $request;
        return Jobtitle::select('id')
            ->selectRaw("concat(tamil,'(',english,')') as name")
            ->where([
                ['active_title', 1],
                ['deleted', 0]
            ])
            ->whereRaw(" `english` like '%" . $request->search_term . "%'")
            ->orderByRaw("INSTR(`english`,'" . $request->search_term . "') asc")
            ->orderBy('english', 'asc')->get();
    }
}
