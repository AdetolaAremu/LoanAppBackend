<?php

namespace App\Http\Controllers\Loan;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoanTypeRequest;
use App\Http\Requests\UpdateLoanTypeRequest;
use App\Http\Resources\LoanTypeResource;
use App\Models\LoanApplication;
use App\Models\LoanType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoanTypeController extends Controller
{
    public function index()
    {
        $loan = LoanType::get();

        return LoanTypeResource::collection($loan , Response::HTTP_ACCEPTED);
    }

    public function store(LoanTypeRequest $request)
    {
        $loan = LoanType::create($request->all());

        return response(['message' => 'Loan type created successfully'], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $loan = LoanType::find($id);

        if (!$loan) {
            return response(['error' => 'Loan type not found'], Response::HTTP_NOT_FOUND);
        }

        return new LoanTypeResource($loan, Response::HTTP_ACCEPTED);
    }

    public function update(UpdateLoanTypeRequest $request, $id)
    {
        $loan = LoanType::find($id);

        if (!$loan) {
            return response(['error' => 'Loan type not found'], Response::HTTP_NOT_FOUND);
        }

        $loan->update($request->only('name','amount','repayment_amount','repayment_days'));

        return response(['message' => 'Loan type updated successfully']);
    }

    public function destroy($id)
    {
        $loan = LoanType::find($id);

        if (!$loan) {
            return response(['error' => 'Loan type not found'], Response::HTTP_NOT_FOUND);
        }

        $loan->delete();

        return response(['message' => 'Loan type deleted successfully']);
    }
}
