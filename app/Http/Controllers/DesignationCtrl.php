<?php

namespace App\Http\Controllers;


use App\Models\DesignationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class DesignationCtrl extends Controller
{
    public function saveDesignation(Request $request)
    {
        if ($request->id) {
            $this->validate($request, [
                'designation' => [
                    'required',
                    Rule::unique('designation', 'designation')->where(function ($query) use ($request) {
                        return $query->where([['deleted', 0], ['id', '!=', $request->id]]);
                    })
                ],

            ]);
        } else {
            $this->validate($request, [
                'designation' => 'required|unique:designation,designation',
            ]);
        }


        $add_employer = array(
            'designation' => $request->designation,
        );

        if ($request->id) {
            DesignationModel::where('id', $request->id)->update([
                'designation' => $request->designation,
            ]);
        } else {
            DB::table('designation')->insertOrIgnore([
                $add_employer
            ]);
        }


        return $this->getDesignation();

    }

    public function getDesignation()
    {
        return DesignationModel::where([['deleted', '0']])->orderBy('id', 'DESC')->get();
    }

    public function getSelectDesignation()
    {
        return DB::connection('read')->table('designation')
            ->select('id as value', 'designation as text')
            ->where('deleted', '=', '0')
            ->orderBy('designation', 'ASC')
            ->get();

    }

    public function getSingleDesignation(Request $request)
    {
        return DesignationModel::where([['deleted', '0']])->where([['id', $request->id]])->orderBy('id', 'DESC')->get();
    }

    public function deleteDesignation(Request $request)
    {
//        return $request;
//        return User::orderBy('id', 'DESC')->get();
        if ($request->id) {

            DesignationModel::where('id', $request->id)->update([
                'deleted' => 1,
            ]);

        }
        return $this->getDesignation();
    }
}
