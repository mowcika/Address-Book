<?php

namespace App\Http\Controllers;

use App\Models\AccessPermission;
use App\Models\MenuMaster;
use App\Models\RoleMaster;
use App\Models\UserRole;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Http\Controllers\MasterController;


class MenuMasterController extends Controller
{
    //Menu master
    public function saveMenuList(Request $request)
    {
//        \DB::enableQueryLog();
        $details = [
            'group_name' => ($request->title) ?: $request->name,
            'route' => $request->route,
            'icon' => str_replace(' ', '', $request->icon),
            'parent' => $request->groupID,
            'position' => $request->position,
        ];
        $request['group_name'] = ($request->name) ?: $request->title;
        $request['id'] = $request->menuID;
        if ($request->menuID) {

            $this->validate($request, [
                'group_name' => [
                    'required',
                    Rule::unique('gcmpro_menu', 'group_name')->where(function ($query) use ($request) {
                        return $query->whereNull('deleted')->where([['id', '!=', $request->id], ['parent', $request->groupID]]);
                    })
                ]
            ]);
            $menu = MenuMaster::find($request->menuID);
            $currentPosition = $menu->position;
            $parentId = $menu->parent;
            MenuMaster::where('id', $request->menuID)
                ->update($details);
            $id = $request->menuID;
        } else {
            // insert new menu
            $this->validate($request, [
                'group_name' => [
                    'required',
                    Rule::unique('gcmpro_menu', 'group_name')->where(function ($query) use ($request) {
                        return $query->whereNull('deleted')->where([['parent', $request->groupID]]);
                    })
                ]
            ]);
            $parentId = $request->groupID;
            $currentPosition = $request->maxPosition;
            $id = MenuMaster::insertGetId($details);
        }

        // adjust position of other menus in the same group
        if ($currentPosition != $request->position) {
            $increment = ($currentPosition < $request->position) ? -1 : 1;
            $positions = MenuMaster::where('parent', $parentId)
                ->where('id', '<>', $id)
                ->where('position', '<>', $currentPosition)
                ->orderBy('position')
                ->get(['id', 'position']);
            echo "Increment = " . $increment . "\n";
            foreach ($positions as $position) {
                if ($increment == 1 && $position->position >= $request->position && $position->position <= $currentPosition) {
                    $newPosition = $position->position + 1;
                    echo "1ID" . $position->id . " --> newPosition = " . $newPosition . "\n";
                } else if ($increment == -1 && $position->position >= $currentPosition && $position->position <= $request->position) {
                    $newPosition = $position->position - 1;
                    echo "2ID" . $position->id . " --> newPosition = " . $newPosition . "\n";
                } else {
                    $newPosition = $position->position;
                    echo "3ID" . $position->id . " --> newPosition = " . $newPosition . "\n";
                }
                MenuMaster::where('id', $position->id)
                    ->update(['position' => $newPosition]);
            }
        }
    }

    public function deleteMenuList(Request $request)
    {
//        \DB::enableQueryLog();
        if ($request->id) {

            MenuMaster::where('id', $request->id)
                ->update(['deleted' => 1]);
        }
    }

    public function showMenuGroup()
    {
        function array_values_recursive($arr, $key)
        {
            $arr2 = ($key == 'children') ? array_values($arr) : $arr;
            foreach ($arr2 as $key => &$value) {
                if (is_array($value)) {
                    $value = array_values_recursive($value, $key);
                }
            }
            return $arr2;
        }

        $data = DB::table('gcmpro_menu AS t1')
            ->leftJoin('gcmpro_menu AS t2', function ($join) {
                $join->on('t2.parent', '=', 't1.id');
                $join->whereNull('t2.deleted');
            })
            ->leftJoin('gcmpro_menu AS t3', function ($join) {
                $join->on('t3.parent', '=', 't2.id');
                $join->whereNull('t3.deleted');
            })
//            ->select('id', 'group_name', 'parent', 'route', 'icon')
            ->select(
                't1.group_name AS lev1',
                't1.id         AS t1id',
                't1.route      AS t1route',
                't1.icon       AS t1icon',
                't1.position   AS t1position',
                't2.group_name as lev2',
                't2.id         AS t2id',
                't2.route      AS t2route',
                't2.icon       AS t2icon',
                't2.position   AS t2position',
                't3.group_name as lev3',
                't3.id         AS t3id',
                't3.route      AS t3route',
                't3.icon       AS t3icon',
                't3.position   AS t3position'
            )
            ->whereNull('t1.deleted')
            ->whereNull('t1.parent')
            ->orderBy('t1.position', 'asc')
            ->orderBy('t1.id', 'asc')
            ->orderBy('t2.position', 'asc')
            ->orderBy('t2.id', 'asc')
            ->orderBy('t3.position', 'asc')
            ->orderBy('t3.id', 'asc')
            ->get();
        $text = array();
        foreach ($data as $value) {
            if ($value->t1id && $value->t2id && $value->t3id) {
                $text[$value->t1id]['title'] = $value->lev1;
                $text[$value->t1id]['name'] = $value->t1id;
                $text[$value->t1id]['expanded'] = true;
                $text[$value->t1id]['position'] = $value->t1position;
                $text[$value->t1id]['children'][$value->t2id]['title'] = $value->lev2;
                $text[$value->t1id]['children'][$value->t2id]['name'] = $value->t2id;
                $text[$value->t1id]['children'][$value->t2id]['expanded'] = true;
                $text[$value->t1id]['children'][$value->t2id]['position'] = $value->t2position;;
                $text[$value->t1id]['children'][$value->t2id]['children'][$value->t3id] = array(
                    'title' => $value->lev3,
                    'name' => $value->t3id,
                    'route' => $value->t3route,
                    'icon' => $value->t3icon,
                    'position' => $value->t3position,
                    'expanded' => true,
                    'depth' => 3,
                );
            } elseif ($value->t1id && $value->t2id) {
                $text[$value->t1id]['title'] = $value->lev1;
                $text[$value->t1id]['name'] = $value->t1id;
                $text[$value->t1id]['expanded'] = true;
                $text[$value->t1id]['position'] = $value->t1position;
                $text[$value->t1id]['children'][$value->t2id] = array(
                    'title' => $value->lev2,
                    'name' => $value->t2id,
                    'route' => $value->t2route,
                    'icon' => $value->t2icon,
                    'position' => $value->t2position,
                    'expanded' => true,
                    'depth' => 2,
                );
            } elseif ($value->t1id) {
                $text[$value->t1id] = array(
                    'title' => $value->lev1,
                    'name' => $value->t1id,
                    'route' => $value->t1route,
                    'icon' => $value->t1icon,
                    'position' => $value->t1position,
                    'expanded' => true,
                    'depth' => 1,
                );
            }
        }
        return array_values_recursive($text, 'children');
//        return $data;
    }

    //Menu show
    public function getMenuBarToShow(Request $request)
    {
        $user_id = $request->user_id;
        $userRole = UserRole::select('role_id')->where('user_id', '=', $user_id)->get();
        if (count($userRole) > 0) {
            $accPerm = AccessPermission::select('menu_id')->where('role_id', '=', $userRole[0]['role_id'])->get();
        } else {
            $accPerm = AccessPermission::select('menu_id')->where('role_id', '=', 2)->get(); //Not updated role for the user will be consider as Client
        }
        $accPermValue = array_column(json_decode(json_encode($accPerm)), 'menu_id');
        function array_values_recursive($arr, $key)
        {
            $arr2 = ($key == 'children') ? array_values($arr) : $arr;
            foreach ($arr2 as $key => &$value) {
                if (is_array($value)) {
                    $value = array_values_recursive($value, $key);
                }
            }
            return $arr2;
        }

        $data = DB::table('gcmpro_menu AS t1')
            ->leftJoin('gcmpro_menu AS t2', function ($join) {
                $join->on('t2.parent', '=', 't1.id');
                $join->whereNull('t2.deleted');
            })
            ->leftJoin('gcmpro_menu AS t3', function ($join) {
                $join->on('t3.parent', '=', 't2.id');
                $join->whereNull('t3.deleted');
            })
            ->select(
                't1.group_name AS lev1',
                't1.id         AS t1id',
                't1.route      AS t1route',
                't1.icon       AS t1icon',
                't1.position   AS t1position',
                't2.group_name as lev2',
                't2.id         AS t2id',
                't2.route      AS t2route',
                't2.icon       AS t2icon',
                't2.position   AS t2position',
                't3.group_name as lev3',
                't3.id         AS t3id',
                't3.route      AS t3route',
                't3.icon       AS t3icon',
                't3.position   AS t3position'
            )
            ->whereNull('t1.deleted')
            ->whereNull('t1.parent')
//            ->whereIn('t3.id', $accPermValue, 'OR', 't2.id', $accPermValue)
            ->where(function ($query) use ($accPermValue) {
                $query->whereIn('t3.id', $accPermValue)
                    ->orWhereIn('t2.id', $accPermValue);
            })
            ->orderBy('t1.position', 'asc')
            ->orderBy('t1.id', 'asc')
            ->orderBy('t2.position', 'asc')
            ->orderBy('t2.id', 'asc')
            ->orderBy('t3.position', 'asc')
            ->orderBy('t3.id', 'asc')
            ->get();
        $text = array();
        foreach ($data as $value) {
            if ($value->t1id && $value->t2id && $value->t3id) {
                $text[$value->t1id]['header'] = $value->lev1;
                $text[$value->t2id]['title'] = $value->lev2;
                $text[$value->t2id]['icon'] = $value->t2icon;
                $text[$value->t2id]['children'][$value->t3id] = array(
                    'title' => $value->lev3,
                    'route' => $value->t3route,
                    'icon' => $value->t3icon,
                );
            } elseif ($value->t1id && $value->t2id) {
                $text[$value->t1id]['header'] = $value->lev1;
                $text[$value->t2id]['title'] = $value->lev2;
                $text[$value->t2id]['icon'] = $value->t2icon;
                $text[$value->t2id]['route'] = $value->t2route;
//                $text[$value->t1id][$value->t2id] = array(
//                    'title' => $value->lev2,
//                    'route' => $value->t2route,
//                    'icon' => $value->t2icon,
//                );
            } elseif ($value->t1id) {
                $text[$value->t1id] = array(
                    'header' => $value->lev1,
                    'route' => $value->t1route,
                    'icon' => $value->t1icon,
                );
            }
        }
        return array_values_recursive($text, 'children');
//        return $data;
    }

    public function getMenuSelect()
    {
        function array_values_recursive($arr, $key)
        {
            $arr2 = ($key == 'children') ? array_values($arr) : $arr;
            foreach ($arr2 as $key => &$value) {
                if (is_array($value)) {
                    $value = array_values_recursive($value, $key);
                }
            }
            return $arr2;
        }

        $data = DB::table('gcmpro_menu AS t1')
            ->leftJoin('gcmpro_menu AS t2', function ($join) {
                $join->on('t2.parent', '=', 't1.id');
                $join->whereNull('t2.deleted');
            })
            ->leftJoin('gcmpro_menu AS t3', function ($join) {
                $join->on('t3.parent', '=', 't2.id');
                $join->whereNull('t3.deleted');
            })
//            ->select('id', 'group_name', 'parent', 'route', 'icon')
            ->select(
                't1.group_name AS lev1',
                't1.id         AS t1id',
                't1.route      AS t1route',
                't1.icon       AS t1icon',
                't1.position   AS t1position',
                't2.group_name as lev2',
                't2.id         AS t2id',
                't2.route      AS t2route',
                't2.icon       AS t2icon',
                't2.position   AS t2position',
                't3.group_name as lev3',
                't3.id         AS t3id',
                't3.route      AS t3route',
                't3.icon       AS t3icon',
                't3.position   AS t3position'
            )
            ->whereNull('t1.deleted')
            ->whereNull('t1.parent')
            ->orderBy('t1.position', 'asc')
            ->orderBy('t1.id', 'asc')
            ->orderBy('t2.position', 'asc')
            ->orderBy('t2.id', 'asc')
            ->orderBy('t3.position', 'asc')
            ->orderBy('t3.id', 'asc')
            ->get();
        $text = array();
        foreach ($data as $value) {
            if ($value->t1id && $value->t2id && $value->t3id) {
                $text[$value->t1id]['id'] = $value->t1id;
                $text[$value->t1id]['label'] = $value->lev1;
                $text[$value->t1id]['children'][$value->t2id]['id'] = $value->t2id;
                $text[$value->t1id]['children'][$value->t2id]['label'] = $value->lev2;
                $text[$value->t1id]['children'][$value->t2id]['children'][$value->t3id] = array(
                    'id' => $value->t3id,
                    'label' => $value->lev3,
                );
            } elseif ($value->t1id && $value->t2id) {
                $text[$value->t1id]['id'] = $value->t1id;
                $text[$value->t1id]['label'] = $value->lev1;
                $text[$value->t1id]['children'][$value->t2id] = array(
                    'id' => $value->t2id,
                    'label' => $value->lev2,
                );
            } elseif ($value->t1id) {
                $text[$value->t1id] = array(
                    'id' => $value->t1id,
                    'label' => $value->lev1,
                );
            }
        }
        return array_values_recursive($text, 'children');
//        return $data;
    }

    public function saveAccessPermission(Request $request)
    {
//        return $request;
        $data = array();
        if ($request->method() == 'POST') {
            $data['role_id'] = $request->role_id;

            $data['cby'] = $request->cby;
            $data['cip'] = $request->ip();
            $data['cdate'] = now()->toDateTimeString();
            $menu_id = $request->menu_id;

            $menu_id['newMenuId'] = $request->menu_id;
            $menu_id['oldMenu'] = AccessPermission::select('menu_id')->where('role_id', $data['role_id'])->get();
            $menu_id['oldMenuId'] = array_column(json_decode(json_encode($menu_id['oldMenu'])), 'menu_id');
            $menu_id['insert'] = array_diff($menu_id['newMenuId'], $menu_id['oldMenuId']);
            $menu_id['delete'] = array_diff($menu_id['oldMenuId'], $menu_id['newMenuId']);
            AccessPermission::where('menu_id', $menu_id['delete'])->delete();

            foreach ($menu_id['insert'] as $key => $value) {
                $data['menu_id'] = $value;
                AccessPermission::insert([$data]);
            }
//            return $datas['query'] = (new MasterController())->getActiveRole();

            $masterController = app()->make(menuMasterController::class);
            return $datas['query'] = $masterController->getActiveRole();

        }
    }

    public function getRoleWithAccessPremission(Request $request)
    {
//         return $roleMaster = RoleMaster::leftJoin('accessPermission as ap', 'role_master.id', '=', 'ap.role_id')
//            ->selectRaw('role_master.id as role_id, role_master.role, ap.id,  ap.role_id as apRoleId')
//             ->selectRaw('GROUP_CONCAT(ap.menu_id) as menu_id')
//            ->where('role_master.isActive', '=', NULL)
//             ->groupBy('role_master.id')
//            ->orderBy('role_master.id', 'DESC')->get();

        $roleMaster = RoleMaster::select('id as role_id', 'role')->where('isActive', '=', NULL)->get();
        $accessPremission = AccessPermission::selectRaw('GROUP_CONCAT(menu_id) as menu_id, GROUP_CONCAT(group_name) as menuName, role_id')
            ->leftJoin('gcmpro_menu as mm', 'accessPermission.menu_id', '=', 'mm.id')
            ->groupBy('role_id')
            ->get();
        $menuMaster = MenuMaster::select('id as menuMasterId', 'group_name as menuName')
            ->where('deleted', '=', NULL)->toSql();


        $resultroleMaster = collect(json_decode($roleMaster, true));
        $resultAccessPremission = collect(json_decode($accessPremission, true));
        $resultMenuMaster = collect(json_decode($menuMaster, true));

        $check = array();
        $roleWithAccessPremission = $resultroleMaster->map(function ($item, $key) use ($resultAccessPremission, $resultMenuMaster) {
            $rolePremission = collect();
            $rolePremission = $resultAccessPremission->where('role_id', $item['role_id'])
                ->values();
            if (sizeof($rolePremission) > 0)
                return collect($item)->merge(collect($rolePremission[0]));
            else
                return collect($item)->merge(collect(array('menu_id' => '')));
        });
        return $roleWithAccessPremission;
    }

    public function getSingleRoleAccess(Request $request)
    {
//        return $request;
        $roleMaster = RoleMaster::select('id as role_id', 'role')->where('id', '=', $request->id)->get();
        $accessPremission = AccessPermission::selectRaw('GROUP_CONCAT(menu_id) as menu_id, role_id')
            ->where('role_id', '=', $request->id)
            ->groupBy('role_id')
            ->get();
//         return $roleMaster;
        $resultroleMaster = collect(json_decode($roleMaster, true));
        $resultAccessPremission = collect(json_decode($accessPremission, true));

        $check = array();
        $roleWithAccessPremission = $resultroleMaster->map(function ($item, $key) use ($resultAccessPremission, $check) {
            $rolePremission = collect();
            $rolePremission = $resultAccessPremission->where('role_id', $item['role_id'])
                ->values();
            if (sizeof($rolePremission) > 0) {
                return collect($item)->merge(collect($rolePremission[0]));
            } else {
//                return collect($item)->merge(collect(array('menu_id' => '')));
                $item['menu_id'] = "0";
                return $item;
            }
        });
        return $roleWithAccessPremission;
    }


    //Role Master
    public function getAllRole()
    {
        return RoleMaster:: orderBy('id', 'DESC')->get();
    }

    public function getActiveRole()
    {
        return RoleMaster::where('isActive', '=', NULL)->orderBy('id', 'DESC')->get();
    }

    public function saveRole(Request $request)
    {
//        return $request;
        $data = array();
        $datas['query'] = $this->getAllRole();
        if ($request->method() == 'POST') {
            $data['role'] = $request->role;
            if ($request->id) {
//                return $request;
                $data['mby'] = $request->cby;
                $data['mip'] = $request->ip();
                $data['mdate'] = now()->toDateTimeString();
                $result_edit = RoleMaster::where("role", '=', $data['role'])->Where('id', '<>', $request->id)->exists();
                if ($result_edit) {
                    $datas['show'] = 'Role Name Already Exists';
                    $datas['check'] = '1';
                    $datas['query'] = $this->getAllRole();
                    return $datas;
                } else {
                    DB::table('role_master')
                        ->where('id', $request->id)
                        ->update($data);
                }
                return $this->getAllRole();
            } else {
                $result = RoleMaster::where("role", $data['role'])->exists();
                if ($result) {
//                    return $result;
                    $datas['show'] = 'Role Name Already Exists';
                    $datas['check'] = '1';
                    $datas['query'] = $this->getAllRole();
                    return $datas;
                } else {
                    $data['cby'] = $request->cby;
                    $data['cip'] = $request->ip();
                    $data['cdate'] = now()->toDateTimeString();

                    DB::table('role_master')->insert([$data]);
//                    $datas['query'] = $this->getAllRole();
                    return $datas['query'] = $this->getAllRole();
                }
            }
        }
    }

    public function getSingleRole(Request $request)
    {
        return RoleMaster::where([['id', $request->id]])->orderBy('id', 'DESC')->get();
    }

    public function activateRole(Request $request)
    {
        if ($request->id) {
            DB::table('role_master')->where('id', $request->id)->update([
                'isActive' => NULL,
            ]);
        }
        return $this->getAllRole();
    }

    public function inActivateRole(Request $request)
    {
        if ($request->id) {
            DB::table('role_master')->where('id', $request->id)->update([
                'isActive' => 1,
            ]);
        }
        return $this->getAllRole();
    }


    //User Role
    public function getUserWithRole()
    {
        $user_teams = [0, 3, 4, 6, 7, 8, 9, 14, 19, 26, 27];
        $nithraUsers = DB::connection('read')->table('nithrausers')
            ->select('id', 'username', 'user_teams')
            ->whereIn('user_teams', $user_teams)
            ->orderBy('id', 'ASC')->get();

        $userRole = UserRole::select('user_id', 'role_id')->orderBy('id', 'DESC')->get();

        $allRole = RoleMaster::select('id as roleId', 'role')->orderBy('id', 'DESC')->get();

        $allTeams = DB::connection('nithra')->table('teams')
            ->select('id as team_id', 'teams')->orderBy('id', 'DESC')->get();

        $resultNithraUsers = collect(json_decode($nithraUsers, true));
        $setResultUserRole = collect(json_decode($userRole, true));
        $setResultRole = collect(json_decode($allRole, true));
        $setResultTeams = collect(json_decode($allTeams, true));

        $setResultTeamsArray = array();
        foreach ($setResultTeams as $teamObject) {
            $setResultTeamsArray[$teamObject['team_id']] = $teamObject['teams'];
        }
        $mergeUserRole = $resultNithraUsers->map(function ($item, $key) use ($setResultUserRole, $setResultRole, $setResultTeams, $setResultTeamsArray) {
            $userWithRoleId = collect();
            $userWithRoleId = $setResultUserRole->where('user_id', $item['id'])
                ->values();

            $item['teamName'] = isset($setResultTeamsArray[$item['user_teams']]) ? $setResultTeamsArray[$item['user_teams']] : "";
            if (sizeof($userWithRoleId) > 0) {
                $userWithRoleIdTemp = json_decode($userWithRoleId, true);
                $mergeRoleId = $setResultRole->where('roleId', $userWithRoleIdTemp[0]['role_id'])
                    ->first();
                return array_merge($item, array('role' => $mergeRoleId['role'], 'role_id' => $userWithRoleIdTemp[0]['role_id']));
            } else
                return array_merge($item, array('role' => " --- ", 'role_id' => ""));
        });
        return $mergeUserRole;
    }

    public function getAllUserRole()
    {
        return DB::connection('mysql')->table('user_role')
            ->orderBy('id', 'DESC')->get();
//        return UserRole::orderBy('id', 'DESC')->get();
    }

    public function getSingleUserRole(Request $request)
    {
        $userDetails = DB::connection('read')->table('nithrausers')
            ->where('id', '=', $request->id)->orderBy('id', 'DESC')->get();
        $userDetailsArray = collect(json_decode($userDetails, true));

        $result = UserRole::where("user_id", $request->id)->exists();
        if ($result) {
            $userRoleDetails = UserRole::where([['user_id', $request->id]])->orderBy('id', 'DESC')->get();
            $userRoleArray = json_decode(json_encode($userRoleDetails), true);
            $userRoleArray[0]['username'] = $userDetails[0]->username;
            return $userRoleArray;
        } else {
            return $userDetails;
        }
    }

    public function saveUserRole(Request $request)
    {
//        return $request;
        $data = array();
//        $datas['query'] = $this->getAllUserRole();
        if ($request->method() == 'POST') {
            $data['role_id'] = $request->role_id;
            $data['user_id'] = $request->id;
            if ($request->id) {
//                return $request;

                $result = UserRole::where("user_id", $request->id)->exists();
                if ($result) {
//                    return $result;
                    $data['mby'] = $request->cby;
                    $data['mip'] = $request->ip();
                    $data['mdate'] = now()->toDateTimeString();
                    DB::table('user_role')->where('user_id', $data['user_id'])
                        ->update($data);
                } else {
                    $data['cby'] = $request->cby;
                    $data['cip'] = $request->ip();
                    $data['cdate'] = now()->toDateTimeString();
                    UserRole::insert([$data]);

                }
            }
            return $datas['query'] = $this->getUserWithRole();
        }
    }
}



