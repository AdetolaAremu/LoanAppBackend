<?php

namespace App\Http\Controllers\KYC;

use App\Http\Controllers\Controller;
use App\Http\Requests\KYCRequest;
use App\Http\Requests\UpdateKYCRequest;
use App\Models\KnowCustomer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class KYCController extends Controller
{    
    public function store(KYCRequest $request)
    {
        KnowCustomer::create($request->all());

        return response(['message' => 'KYC request submitted successfully'], Response::HTTP_CREATED);
    }

    public function index()
    {
        $kyc = KnowCustomer::get();

        return response($kyc, Response::HTTP_ACCEPTED);
    }

    public function show($id)
    {
        $kyc = KnowCustomer::find($id);

        if (!$kyc) {
            return response(['message' => 'KYC not found'], Response::HTTP_NOT_FOUND);
        }

        return response($kyc, Response::HTTP_ACCEPTED);
    }

    public function update(UpdateKYCRequest $request, $id)
    {
        $kyc = KnowCustomer::find($id);

        if (!$kyc) {
            return response(['message' => 'KYC not found'], Response::HTTP_NOT_FOUND);
        }

        $kyc->update($request->all());

        return response(['message' => 'KYC updated successfully'], Response::HTTP_ACCEPTED);
    }

    public function destroy($id)
    {
        $kyc = KnowCustomer::find($id);

        if (!$kyc) {
            return response(['message' => 'KYC not found'], Response::HTTP_NOT_FOUND);
        }

        $kyc->delete();

        return response(['message' => 'KYC deleted successfully'], Response::HTTP_ACCEPTED);
    }

    public function verifyUser($id, Request $request)
    {
        $kyc = KnowCustomer::find($id);

        if (!$kyc) {
            return response(['message' => 'KYC not found'], Response::HTTP_NOT_FOUND);
        }

        $kyc->update($request->only('status'));

        return response(['message' => 'Change effected successfully'], Response::HTTP_ACCEPTED);
    }
}
