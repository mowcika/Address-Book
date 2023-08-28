<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usergroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class UsergroupController extends Controller
{
    public function index()
    {
        return Usergroup::whereNull('deleted')->orderBy('id', 'ASC')->get();
    }

    public function getSingleUserGroup(Request $request)
    {
        return Usergroup::whereNull('deleted')->where([['id', $request->id]])->orderBy('id', 'DESC')->get();
    }

    public function saveUserGroup(Request $request)
    {
//        \DB::enableQueryLog();
//        return $request;
//        return Usergroup::orderBy('id', 'DESC')->get();
        if ($request->id) {
            $this->validate($request, [
                'name' => [
                    'required',
                    Rule::unique('usergroup', 'name')->where(function ($query) use ($request) {
                        return $query->whereNull('deleted')->where([['id', '!=', $request->id]]);
                    })
                ]
            ]);

            Usergroup::where('id', $request->id)->update([
                'id' => $request->id,
                'name' => $request->name,
            ]);

        } else {
            $this->validate($request, [
                'name' => [
                    'required',
                    Rule::unique('usergroup', 'name')->where(function ($query) {
                        return $query->whereNull('deleted');
                    })
                ]
            ]);

            DB::table('usergroup')->insertOrIgnore([
                'name' => $request->name,
            ]);
        }
        return $this->index();
//        ddd(\DB::getQueryLog());
    }

    public function deleteUserGroup(Request $request)
    {
//        return $request;
//        return Usergroup::orderBy('id', 'DESC')->get();
        if ($request->id) {

            Usergroup::where('id', $request->id)->update([
                'deleted' => 1,
            ]);

        }
        return $this->index();
    }

}
