<?php

namespace App\Http\Controllers;

use Carbon\Traits\Date;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\APIemployerModel;

//use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mime\Header\Headers;


class APIemployerController extends Controller
{

    public function apiEmployerLogin(Request $request)
    {
        $APIemployerModel = new APIemployerModel();
//        return $APIemployerModel->tokenCan('server:update');exit;
        $token = $APIemployerModel->createToken($request->token_name);

        return ['token' => $token->plainTextToken];
    }

    public function apiEmployerValidate(Request $request)
    {
        try {
            $token = $request->bearerToken();

            $user = APIemployerModel::where('token', $token)->first();

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getCompanySource()
    {
        if (Redis::exists('getCompanySource')) {
            $result = unserialize(Redis::get('getCompanySource'));
            $result['from'] = 'redis';
        } else {
            $result['getFreeDistrict'] = $this->getFreeDistrict('freecalling_details', ['district as id', DB::raw('CONCAT(IF(district IS NULL OR district = "", "No District", district), " (",COUNT(*), ")") as name')]);
            $result['getUsers'] = $this->getUsers('user as u', ['u.id', 'u.name', 'u.role', 'u.usergroup']);
            $result['companySource'] = $this->getResource('company_source', ['id', 'source as name'], [['is_delete', 0]], 'id', [4, 25, 31]);
            $result['getFreeCallingUploadExcel'] = $this->getFreeCallingUploadExcel();
            $result['from'] = 'database';
            Redis::set('getCompanySource', serialize($result));
            Redis::expire('getCompanySource', 86400);
        }
        return $result;
    }
}
