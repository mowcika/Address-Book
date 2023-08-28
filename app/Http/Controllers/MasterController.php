<?php

namespace App\Http\Controllers;

use App\Models\Jobtitle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use Predis\Command\Redis\RPOP;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Exceptions\ShellCommandFailedException;
use Illuminate\Support\Facades\Http;
use App\Models\FcmModel;

class MasterController extends Controller
{
    public function getResource($table_name, $columns)
    {
        return DB::table($table_name)
            ->select($columns)
            ->get();
    }

    public function getMaster()
    {
        $result['notiType'] = $this->getResource('noti_type', ['id', 'noti as name']);
        $result['appMaster'] = $this->getResource('app_master', ['id', 'app_name as name']);
        $result['msgType'] = $this->getResource('msg_type', ['id', 'type as name']);
        $result['vcode'] = $this->getVcode();

        return $result;

    }

    public function getVcode()
    {
        return FcmModel::where([['is_delete', 0]])->groupBy('vcode')->orderBy('vcode', 'ASC')->get(['vcode as id', 'vcode as name']);
    }
}
