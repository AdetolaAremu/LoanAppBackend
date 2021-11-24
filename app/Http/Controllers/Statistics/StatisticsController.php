<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\KnowCustomer;
use App\Models\LoanApplication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StatisticsController extends Controller
{
    // count of all kyc status
    public function allStatus()
    {
        $count = array("kycPending" => 0, "kycSuccessful" => 0, "kycFailed" => 0, "loanPending" => 0, "loanSuccessful" => 0, "loanFailed" => 0);
        $count['kycPending'] = KnowCustomer::where('status', 'pending')->count();
        $count['kycSuccessful'] = KnowCustomer::where('status', 'successful')->count();
        $count['kycFailed'] = KnowCustomer::where('status', 'failed')->count();

        $count['loanPending'] = LoanApplication::where('loan_status', 'pending')->count();
        $count['loanSuccessful'] = LoanApplication::where('loan_status', 'accepted')->count();
        $count['loanFailed'] = LoanApplication::where('loan_status', 'failed')->count();

        return response($count, Response::HTTP_OK);
    }

    // count of all loan counts
    public function loanCount()
    {
        $count = array("Pending" => 0, "Successful" => 0, "Failed" => 0);
        $count['Pending'] = LoanApplication::where('loan_status', 'pending')->count();
        $count['Successful'] = LoanApplication::where('loan_status', 'accepted')->count();
        $count['Failed'] = LoanApplication::where('loan_status', 'failed')->count();

        return response($count, Response::HTTP_OK);
    }

    // dashboard count
    public function dashboardCount()
    {
        $count = array("LoanCount" => 0, "Users" => 0, "dailyLoanCount" => 0, "allLoans" => 0);
        $count['LoanCount'] = LoanApplication::where('active', 1)->count();
        $count['Users'] = User::count();
        $count['dailyLoanCount'] = LoanApplication::where('created_at', '>=', Carbon::today())->count();
        $count['allLoans'] = LoanApplication::count();
        
        return response($count, Response::HTTP_OK);
    }

    // get the last users that registered
    public function latestUsers()
    {
        $user = User::latest()->take(5)->get();

        return UserResource::collection($user, Response::HTTP_OK);
    }
}
