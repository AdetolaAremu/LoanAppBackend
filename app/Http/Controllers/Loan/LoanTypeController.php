<?php

namespace App\Http\Controllers\Loan;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoanTypeRequest;
use App\Http\Requests\UpdateLoanTypeRequest;
use App\Http\Resources\LoanTypeResource;
use App\Models\LoanType;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class LoanTypeController extends Controller
{
    // get all loan types with the count of loans associated with the loan type
    public function index()
    {
        // only a user with view users permissions can view this resource
        Gate::authorize('view', 'users');

        $loan = LoanType::withCount("loanApplication")->get();

        return response($loan, Response::HTTP_ACCEPTED);
    }

    // store a new loan type
    public function store(LoanTypeRequest $request)
    {
        Gate::authorize('view', 'users');
        
        LoanType::create($request->all());

        return response(['message' => 'Loan type created successfully'], Response::HTTP_CREATED);
    }

    // get a loan type
    public function show($id)
    {
        Gate::authorize('view', 'users');

        $loan = LoanType::find($id);

        if (!$loan) {
            return response(['error' => 'Loan type not found'], Response::HTTP_NOT_FOUND);
        }

        return new LoanTypeResource($loan, Response::HTTP_ACCEPTED);
    }

    // update a loan type
    public function update(UpdateLoanTypeRequest $request, $id)
    {
        Gate::authorize('view', 'users');
        
        $loan = LoanType::find($id);

        if (!$loan) {
            return response(['error' => 'Loan type not found'], Response::HTTP_NOT_FOUND);
        }

        $loan->update($request->only('name','amount','repayment_amount','repayment_days'));

        return response(['message' => 'Loan type updated successfully']);
    }

    // delete a loan type
    public function destroy($id)
    {
        $loan = LoanType::find($id);

        if (!$loan) {
            return response(['error' => 'Loan type not found'], Response::HTTP_NOT_FOUND);
        }

        $loan->delete();

        return response(['message' => 'Loan type deleted successfully'], Response::HTTP_OK);
    }
}
