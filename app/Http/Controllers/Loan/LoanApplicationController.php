<?php

namespace App\Http\Controllers\Loan;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoanApplicationRequest;
use App\Models\LoanApplication;
use App\Models\LoanApplicationComment;
use App\Models\LoanGuarantor;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LoanApplicationController extends Controller
{
    // get all loan applications
    public function index()
    {
        // only a user with view users permissions can view this resource
        Gate::authorize('view', 'users');

        $loan = LoanApplication::get();

        return response($loan, Response::HTTP_OK);
    }

    // get logged user loan applications
    public function getUserLoan()
    {
        $loan = LoanApplication::where('user_id', auth()->user()->id)
            ->with('user','guarantor','loanType','comment')->latest()->get();

        return response($loan, Response::HTTP_OK);
    }

    // create a new loan request, it will create data in two tables
    public function store(LoanApplicationRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $loan = new LoanApplication();
            $loan->user_id = Auth::user()->id;
            $loan->loan_type_id = $request->loan_type_id;
            $loan->reason = $request->reason;
            $loan->bank_name = $request->bank_name;
            $loan->account_number = $request->account_number;
            $loan->account_type = $request->account_type;
            $loan->save();

            $guarantor = new LoanGuarantor(); 
            $guarantor->loan_application_id = $loan->id;
            $guarantor->full_name = $request->full_name;
            $guarantor->address = $request->address;
            $guarantor->phone = $request->phone;
            $guarantor->relationship = $request->relationship;
            $guarantor->email = $request->email;
            $guarantor->save();
      
            DB::commit();
            return response(['message' => 'Loan Application created successfully, it is now under review'], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th, Response::HTTP_NOT_ACCEPTABLE);
        }
    }

    // get a loan application resource
    public function show($id)
    {
        $loan = LoanApplication::with('user','guarantor','loanType','comment')->find($id);

        if (!$loan) {
            return response(['error' => 'Loan not found'], Response::HTTP_NOT_FOUND);
        }

        return response($loan, Response::HTTP_OK);
    }

    // update an application resource
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $loan = LoanApplication::where('id', $id)->with('guarantor')->first();
            $loan->user_id = Auth::user()->id;
            $loan->loan_type_id = $request->loan_type_id ?? $loan->loan_type_id;
            $loan->reason = $request->reason ?? $loan->reason;
            $loan->bank_name = $request->bank_name ?? $loan->bank_name;
            $loan->account_number = $request->account_number ?? $loan->account_number;
            $loan->account_type = $request->account_type ?? $loan->account_type;
            $loan->update();

            $guarantor = LoanGuarantor::where('loan_application_id', $loan->id)->first();
            $guarantor->loan_application_id = $loan->id;
            $guarantor->full_name = $request->full_name ?? $guarantor->full_name;
            $guarantor->address = $request->address ?? $guarantor->address;
            $guarantor->phone = $request->phone ?? $guarantor->phone;
            $guarantor->relationship = $request->relationship ?? $guarantor->relationship;
            $guarantor->email = $request->email ?? $guarantor->email;
            $guarantor->update();
            
            DB::commit();
            return response(['message' => 'Loan Application updated successfully'], Response::HTTP_ACCEPTED);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response($th, Response::HTTP_NOT_ACCEPTABLE);
        }
    }

    // delete a loan application resource
    public function destroy($id)
    {
        $loan = LoanApplication::find($id);

        if (!$loan) {
            return response(['error' => 'Loan not found'], Response::HTTP_NOT_FOUND);
        }

        $loan->delete();

        return response(['message' => 'Loan application deleted successfully'], Response::HTTP_OK);
    }

    // reject loan request with a comment
    public function rejectLoan($id, Request $request)
    {
        $loan = LoanApplication::find($id);
        
        if (!$loan) {
            return response(['message' => 'Loan Application not found'], Response::HTTP_NOT_FOUND);
        }

        $loan->update(['loan_status' => 'failed']);

        if ($loan) {
            
            $request->validate(['comment' => 'required']);

            $com = new LoanApplicationComment();
            $com->loan_application_id = $loan->id;
            $com->comment = $request->comment;
            $com->save();

            return response(['message' => 'Loan rejected'], Response::HTTP_OK);
        }

        return response(['message' => 'Action not effected'], Response::HTTP_BAD_REQUEST);
    }

    // approve a loan request
    public function approveLoan($id)
    {
        $loan = LoanApplication::find($id);
        
        if (!$loan) {
            return response(['message' => 'Loan Application not found'], Response::HTTP_NOT_FOUND);
        }

        $loan->update(['loan_status' => 'accepted']);

        $loan->update(['active' => 1]);

        return response(['message' => 'Loan Approved'], Response::HTTP_OK);
    }

    // if a loan request has been rejected but due to an appeal from the user to review the rejection,
    // the admin can recycle the kyc so it can be worked on again, it will go to te pending bucket
    public function recycleLoan($id)
    {
        $loan = LoanApplication::find($id);
        
        if (!$loan) {
            return response(['message' => 'Loan Application not found'], Response::HTTP_NOT_FOUND);
        }

        $loan->update(['loan_status' => 'pending']);

        return response(['message' => 'Loan Application recycled, check pending bucket!'], Response::HTTP_OK);
    }
 
    // used in filtering status such as pending, successful etc
    // parameter used is $status which can be set to any of status
    public function getStatus($status)
    {
        Gate::authorize('view', 'users');

        $loan = LoanApplication::where("loan_status", $status)->with('user')->get();

        return response($loan, Response::HTTP_OK);
    }

    // get loans that are accepted and have not be repaid
    public function pluckUserStatus()
    {
        $loans = LoanApplication::where('user_id', auth()->user()->id)
                ->where('loan_status', 'accepted')
                ->where('repaid', 0)
                ->select('loan_status', 'repaid')
                ->get();

        return response($loans, Response::HTTP_OK);
    }
}