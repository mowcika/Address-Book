<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class ReportsController extends Controller
{
    // get team
    public function getTeamCounts()
    {
        $result['gTeamCount'] = DB::table('paymentTeamCompetitionMaster as tm')
            ->join('paymentTeamCompetitionMembers as tmm', 'tm.id', '=', 'tmm.teamName')
            ->selectRaw('tm.id, tm.teamName, COUNT(*) as count, GROUP_CONCAT(tmm.members) members')
            ->groupBy('tm.id')
            ->orderByRaw('tm.teamName ASC')
            ->distinct()
            ->get();
        return $result;
    }

    // get job source
    public function getjobSourceData()
    {

        $jobsourceDrop = DB::table('company_source')
            ->selectRaw('id,source')
            ->where('is_delete', '=', '0')
            ->whereIn('id', array(57, 58))
            ->get();
        $result['jobsourceDrop'] = $jobsourceDrop;

        return $result;
    }


    // get payment Month Wise
    public function getpaymentMonthWise(Request $request)
    {
        \DB::enableQueryLog();
//        print_r($request->date);
//        print_r($request->dayDateFilter);exit;
//        print_r($request->uid);exit;
        $dates = $request->date;
        $getFilter = $request->dayDateFilter;
        //	$getFilter = 2; // 1 - date, 2 - day
//        print_r($dates);exit;
        $where = $date = $notdate = array();
        $whereIn = $whereInDate = $whereInnotDate = '1';
        $groupby1 = "";

        $seventhdate = date('Y-m-d', strtotime('-129 days'));
        $todate = date('Y-m-d', strtotime('-0 days'));
        $getFirstDateofMonth = date("Y-m-01", strtotime(date('Ym')));
        $getLastDateofMonth = date("Y-m-t", strtotime(date('Ym')));
        $seventhdate = $getFirstDateofMonth;
        $todate = $getLastDateofMonth;

        if (sizeof($dates)) {
            $dateString = implode(",", $dates);
            // $this->db->where_in('EXTRACT( YEAR_MONTH FROM `fcs`.`txn_date` )', $dateString, false);
            $date[] = "EXTRACT( YEAR_MONTH FROM `fcs`.`txn_date` ) IN ($dateString)";
        }
        if (count($date)) {
            $whereInDate = implode(' AND ', $date);
        }

        if (!sizeof($date)) {
            $notdate[] = "date(fcs.txn_date) BETWEEN $seventhdate and $todate";
            // $this->db->where('date(fcs.txn_date) >= ', $seventhdate);
            // $this->db->where('date(fcs.txn_date) <= ', $todate);
            $date_from = strtotime($seventhdate);
            $date_to = strtotime($todate);
        }
        if (count($notdate)) {
            $whereInnotDate = implode(' AND ', $notdate);
        }
        if ($getFilter == 1 || $getFilter == 3) {
            $groupby1 = "date(fcs.txn_date)";
            // $this->db->group_by('date(fcs.txn_date)');
        } else {
            $groupby1 = "MONTH(fcs.txn_date), DAYNAME(fcs.txn_date)";
            //$this->db->group_by('MONTH(fcs.txn_date), DAYNAME(fcs.txn_date)');
        }
        $orderby = "date(fcs.txn_date) ASC";
        $getData = DB::table('franchise_company_debits as fcs')
            ->selectRaw('DAYNAME(fcs.txn_date) as `dayName`, GROUP_CONCAT(DISTINCT DAY(fcs.txn_date)) as `daysString`, DAY(fcs.txn_date) as `date`, DATE_FORMAT(fcs.txn_date, "%b-%Y") as `month`, date_format(fcs.txn_date, "%d-%m-%Y") as idate, count(*) as invoiceCount, sum((fcs.txn_amt/1.18)) as invoiceAmount
         ')
            ->leftJoin('allot_company_source as acs', 'acs.debit_id', '=', 'fcs.debit_id')
            ->leftJoin('user as u', 'u.id', '=', 'fcs.incentive_userid')
            ->join('paymentTeamCompetitionMembers as ptcb', 'ptcb.members', '=', 'fcs.incentive_userid')
            ->join('paymentTeamCompetitionMaster as ptcm', 'ptcm.id', '=', 'ptcb.teamName')
            ->where('fcs.status', '=', 'TXN_SUCCESS')
            ->whereRaw($whereInDate)
            ->whereRaw($whereInnotDate)
            ->groupByRaw($groupby1)
            ->orderByRaw($orderby)
            ->get();
        // dd(\DB::getQueryLog());
        // print_r($result);exit;
        if ($getFilter == 1 || $getFilter == 3) {
            $datePeriod = range(1, 31);
            $getFinalArray = array();
            $getFinalArray1 = $getFinalLabelArray = array();
            foreach ($getData as $main) {
                foreach ($datePeriod as $value) {
                    if ((date("M-Y") != $main->month) || (date("M-Y") == $main->month && $value <= date('d'))) {
                        $getFinalArray[$main->month]['count'][$value] = 0;
                        $getFinalArray[$main->month]['amount'][$value] = 0;
                    }
                }
            }
            foreach ($getData as $main) {
                $getFinalArray[$main->month]['count'][$main->date] = $main->invoiceCount;
                $getFinalArray[$main->month]['amount'][$main->date] = round($main->invoiceAmount);
            }
            foreach ($getFinalArray as $key => $value) {
                $getFinalLabelArray[] = $key;
                if ($getFilter == 3) {
                    $tempSumValue = 0;
                    foreach ($value['amount'] as $keyVal => $val) {
                        $tempSumValue += $val;
                        $value['amount'][$keyVal] = $tempSumValue;
                    }
                }
                $getFinalArray1[] = $value['amount'];
            }
            $result['getData'] = $getData;
            $result['getLabel'] = $datePeriod;
            $result['finalLabel'] = $getFinalLabelArray;
            $result['getFinal'] = $getFinalArray1;
            //print_r($result['getFinal']);exit;
        } else {
            $daysPeriod = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
            $getFinalArray = array();
            $getFinalArray1 = $getFinalLabelArray = array();
            foreach ($getData as $main) {
                foreach ($daysPeriod as $value) {
                    $getFinalArray[$main->month]['count'][$value] = 0;
                    $getFinalArray[$main->month]['amount'][$value] = 0;
                    $getFinalArray[$main->month]['daysString'][$value] = '';
                }
            }
            foreach ($getData as $main) {
                $getFinalArray[$main->month]['count'][$main->dayName] = $main->invoiceCount;
                $getFinalArray[$main->month]['amount'][$main->dayName] = $main->invoiceAmount;
                $getFinalArray[$main->month]['daysString'][$main->dayName] = $main->daysString;
            }
            foreach ($getFinalArray as $key => $value) {
                $getFinalLabelArray[] = $key;
                $getFinalArray1[] = $value['amount'];
            }
            // print_r($getData);exit;
            $result['getData'] = $getData;
//		$result['getDates'] = $datePeriod;
            $result['getLabel'] = $daysPeriod;
            $result['finalLabel'] = $getFinalLabelArray;
            $result['getFinal'] = $getFinalArray1;
            // print_r($getFinalLabelArray);exit;
        }
        return $result;
    }


    public function getpaymentTeamWise(Request $request)
    {
        $where = $dates = $notdate = $countPayment2 = $countPayment3 = $uG = $dates_n = $js = array();
        $whereIn = $whereInDate = $whereInnotDate = $whereIncountPayment2 = $whereIncountPayment3 = $whereJs_in = $whereInuG = $whereIndates_n = '1';

        $date = $request->date;
        $user = $request->user;
        $userGroup = $request->userGroup;
        $orderBy = $request->orderBy;
        $countPayment = $request->countPayment;
        $filter = $request->filter;
        $uid = $request->uid;
        $jobSource = $request->jobSource;

        $seventhdate = date('Y-m-d', strtotime('-29 days'));
        $todate = date('Y-m-d', strtotime('-0 days'));
        $getFirstDateofMonth = date("Y-m-01", strtotime(date('Ym')));
        $getLastDateofMonth = date("Y-m-t", strtotime(date('Ym')));
        $seventhdate = $getFirstDateofMonth;
        $todate = $getLastDateofMonth;

//        if ($filter == 2) {
//            $tempLabelName = "date(fcs.txn_date) as teamName";
//        } elseif (sizeof(array_filter($userGroup)) != 0) { // $userGroup != ''
//            $tempLabelName = "u.name as teamName";
//        } else {
//            $tempLabelName = "ptcm.teamName";
//        }

        if ($filter == 2) {
            $tempLabelName = "date(fcs.txn_date) as teamName";
        } else {
            if (!empty($userGroup)) { // $userGroup != ''
                $tempLabelName = "u.name as teamName";
                if (in_array("all_team", $userGroup)) {
                    $tempLabelName = "ptcm.teamName";
                }
            }
        }


        $fromdate = date("Y-m-d", strtotime($date['startDate']));
        $todate = date("Y-m-d", strtotime($date['endDate']));

//print_r($date);
//echo "<br>";
//print_r($fromdate);
//echo "<br>";
//print_r($todate);
//exit;
// startDate
        // endDate
        if ($date != '') {
//            $dateSplit = explode('-', $date);
//            $fr = $dateSplit[0];
//            $t = $dateSplit[1];
            $fr = $fromdate;
            $t = $todate;
            $from = date('Y-m-d', strtotime($fr));
            $to = date('Y-m-d', strtotime($t));
            $dates[] = "date(fcs.txn_date) BETWEEN '$from' and '$to'";
            // $this->db->where('date(fcs.txn_date) >= ', $from);
            // $this->db->where('date(fcs.txn_date) <= ', $to);
            $date_from = strtotime($from);
            $date_to = strtotime($to);
        }
        if (count($dates)) {
            $whereInDate = implode(' AND ', $dates);
        }

        if ($countPayment == 2) {
            // $this->db->where('fcs.txn_amt > ', 0);
            $countPayment2[] = "fcs.txn_amt > 0";
        }
        if (count($countPayment2)) {
            $whereIncountPayment2 = implode(' AND ', $countPayment2);
        }

        if ($countPayment == 3) {
            // $this->db->where('fcs.txn_amt = ', 0);
            $countPayment3[] = "fcs.txn_amt = 0";
        }
        if (count($countPayment3)) {
            $whereIncountPayment3 = implode(' AND ', $countPayment3);
        }

        if ($jobSource == 57 || $jobSource == 58) {
            // $this->db->where('fcs.txn_amt = ', 0);
            $js[] = "fcs.jobsource = $jobSource  ";
        }

        if (count($js)) {
            $whereJs_in = implode(' AND ', $js);
        }
        if (count($countPayment3)) {
            $whereIncountPayment3 = implode(' AND ', $countPayment3);
        }
//        print_r($userGroup);exit;
        if (sizeof(array_filter($userGroup)) != 0) {    // $userGroup != ''
// $this->db->where_in('ptcb.teamName', $userGroup, false);
            if (in_array('all', $userGroup)) { // $userGroup == 'all'
                $having = rtrim($orderBy, " DESC") . ' > 0';
                // $this->db->having(rtrim($orderBy, " DESC") . ' > 0');
                // $this->db->group_by('fcs.incentive_userid');
                $groupBy = "fcs.incentive_userid";
            } else if (in_array('all_team', $userGroup)) {
                $having = "1=1";
                $groupBy = "ptcm.id";
            } else {
// $this->db->where('ptcm.id', $userGroup);
                $having = "1=1";
                // $this->db->where_in('ptcm.id', $userGroup);
                $chk = "" . join(', ', $userGroup) . "";
                $uG[] = "ptcm.id IN ($chk)";
                $groupBy = "ptcm.id, fcs.incentive_userid";
                // $this->db->group_by('ptcm.id');
                // $this->db->group_by('fcs.incentive_userid');
                if (count($uG)) {
                    $whereInuG = implode(' AND ', $uG);
                }

            }
        } else {

            $having = "1=1";
            // $this->db->group_by('ptcm.id');
            $groupBy = "ptcm.id";
        }
        if (!$date && !$user) {
// $this->db->where('date(fcs.txn_date) >= ', $seventhdate);
// $this->db->where('date(fcs.txn_date) <= ', $todate);
            $dates_n[] = "date(fcs.txn_date) BETWEEN $seventhdate and $todate";

            if (count($dates_n)) {
                $whereIndates_n = implode(' AND ', $dates_n);
            }
            // $this->db->group_by('ptcm.id');
            $groupBy = "ptcm.id";
            $date_from = strtotime($seventhdate);
            $date_to = strtotime($todate);
        }

        if ($filter == 2) {
            // $this->db->group_by('date(fcs.txn_date)');
            $groupBy = 'date(fcs.txn_date)';
        }


        $getData = DB::table('franchise_company_debits as fcs')
            ->selectRaw($tempLabelName . ', ptcm.id, date_format(fcs.txn_date,"%d-%m-%Y") as idate,u.name as iname,count(fcs.employer_id) as company_count11,
		sum(IFNULL(if(fcs.company_status != "",1,0),0)) AS company_count,
        sum(fcs.txn_amt) as total_amount,
        sum(ROUND(fcs.txn_amt/1.18)) as exc_gst_amount,
        sum(IFNULL(if(fcs.company_status = "New",1,0),0)) AS nv,
        sum(IFNULL(if(fcs.company_status = "New",fcs.txn_amt,0),0)) AS nv_amount,
        sum(IFNULL(if(fcs.company_status = "Old",1,0),0)) AS ol,
        sum(IFNULL(if(fcs.company_status = "Old",fcs.txn_amt,0),0)) AS ol_amount,
        sum(IFNULL(if(fcs.discountIncluded = 1, 1, 0), 0)) AS dl,
		sum(IFNULL(if(fcs.discountIncluded = 1, `fcs`.`txn_amt`, 0), 0)) AS dl_amount,
        fcs.incentive_userid')
            ->leftJoin('allot_company_source as acs', 'acs.debit_id', '=', 'fcs.debit_id')
            ->leftJoin('user as u', 'u.id', '=', 'fcs.incentive_userid')
            ->join('paymentTeamCompetitionMembers as ptcb', 'ptcb.members', '=', 'fcs.incentive_userid')
            ->join('paymentTeamCompetitionMaster as ptcm', 'ptcm.id', '=', 'ptcb.teamName')
            ->where('fcs.status', '=', 'TXN_SUCCESS')
            ->whereRaw($whereInDate)
            ->whereRaw($whereIncountPayment2)
            ->whereRaw($whereIncountPayment3)
            ->whereRaw($whereInuG)
            ->whereRaw($whereIndates_n)
            ->whereRaw($whereJs_in)
            ->havingRaw($having)
            ->groupByRaw($groupBy)
            ->orderByRaw($orderBy)
            ->get();

        // print_r($getData);exit;
        function list_days($date_from, $date_to)
        {
            $arr_days = array();
//			$day_passed = ($date_to - $date_from); //seconds
            $day_passed = ($date_to - $date_from) + 86400 + 86400; // customize for includes start and end dates
            $day_passed = ($day_passed / 86400); //days

            $counter = 1;
            $day_to_display = $date_from - 1; // -1 day customize
            while ($counter < $day_passed) {
                $day_to_display += 86400;
                //echo date("F j, Y \n", $day_to_display);
                $arr_days[] = date('o-m-d', $day_to_display);
                $counter++;
            }

            return $arr_days;
        }

        $datePeriod = array();//list_days($date_from, $date_to);
        // print_r($getData);exit;
        $result['getData'] = $getData;
        $result['getDates'] = $datePeriod;
        return $result;

    }
}
