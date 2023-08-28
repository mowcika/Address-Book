<?php

namespace App\Http\Controllers;

use App\Models\FranchiseCompanyDebitsModel;

class FranchiseCompanyDebitsCtrl extends Controller
{
    public function todayPayment()
    {
        return FranchiseCompanyDebitsModel::selectRaw("sum(txn_amt) as todayPayment")
            ->where([
                ['status', 'TXN_SUCCESS'],
            ])
            ->whereRaw(" date(cdate)=date(now())")
            ->get();
    }

    public function getMonthCompar()
    {
        return FranchiseCompanyDebitsModel::selectRaw("SUM(CASE WHEN date(txn_date) between date(NOW() - INTERVAL 30 day) AND date(NOW())  THEN txn_amt ELSE 0 END) AS this_month_amt")
            ->selectRaw("SUM(CASE WHEN date(txn_date) between date(NOW() - INTERVAL 60 day) AND date(NOW() - INTERVAL 30 day)  THEN txn_amt ELSE 0 END) AS last_month_amt")
            ->where([
                ['status', 'TXN_SUCCESS'],
            ])
            ->get();
    }
}
