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
    public function index()
    {
        $loan = LoanApplication::with('guarantor','loanType')->get();

        return response($loan, Response::HTTP_OK);
    }

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

    public function show($id)
    {
        $loan = LoanApplication::with('guarantor','loanType')->find($id);

        if (!$loan) {
            return response(['error' => 'Loan not found'], Response::HTTP_NOT_FOUND);
        }

        return response($loan, Response::HTTP_OK);
    }

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

    public function destroy($id)
    {
        $loan = LoanApplication::find($id);

        if (!$loan) {
            return response(['error' => 'Loan not found'], Response::HTTP_NOT_FOUND);
        }

        $loan->delete();

        return response(['message' => 'Loan application deleted successfully']);
    }

    public function rejectLoan($id, Request $request)
    {
        $loan = LoanApplication::find($id);
        
        if (!$loan) {
            return response(['message' => 'Loan Application not found']);
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

    public function approveLoan($id)
    {
        $loan = LoanApplication::find($id);
        
        if (!$loan) {
            return response(['message' => 'Loan Application not found']);
        }

        $loan->update(['loan_status' => 'accepted']);

        return response(['message' => 'Loan Approved'], Response::HTTP_OK);
    }

    // public function kycCheck()
    // {
    //     $status = auth()->user()->kyc->pluck('status');

    //     return response($status, Response::HTTP_OK);
    // }

    public function getStatus($status)
    {
        Gate::authorize('view', 'users');

        $loan = LoanApplication::where("loan_status", $status)->with('user')->get();

        return response($loan, Response::HTTP_OK);
    }
}