<?php

namespace App\Http\Controllers\KYC;

use App\Http\Controllers\Controller;
use App\Http\Requests\KYCRequest;
use App\Http\Requests\UpdateKYCRequest;
use App\Models\KnowCustomer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class KYCController extends Controller
{    
    public function store(Request $request)
    {
        // this is to make sure that a user can only have one KYC
        // if the kyc of a user is rejected, on creating another one, the first one will be deleted
        $exist = KnowCustomer::where('user_id', auth()->user()->id)->first();

        if ($exist) {
            $exist->delete();
        }

        $kyc = new KnowCustomer();
        $kyc->user_id = auth()->user()->id;
        $kyc->country_id = $request->country_id;
        $kyc->state_id = $request->state_id;
        $kyc->city = $request->city;
        $kyc->address = $request->address;
        $kyc->identification_type = $request->identification_type;
        $kyc->id_number = $request->id_number;
        $kyc->nok_first_name = $request->nok_first_name;
        $kyc->nok_last_name = $request->nok_last_name;
        $kyc->nok_middle_name = $request->nok_middle_name;
        $kyc->nok_email = $request->nok_email;
        $kyc->nok_phone = $request->nok_phone;
        $kyc->nok_country_id = $request->nok_country_id;
        $kyc->nok_state_id = $request->nok_state_id;
        $kyc->nok_city = $request->nok_city;
        $kyc->nok_address = $request->nok_address;
        $kyc->rejection_reason = $request->rejection_reason;
        $kyc->save();

        return response(['message' => 'KYC request submitted successfully'], Response::HTTP_CREATED);
    }

    // get all kyc for admin
    public function index()
    {
        // only those with view users permission can access this resource
        Gate::authorize('view', 'users');

        $kyc = KnowCustomer::get();

        return response($kyc, Response::HTTP_OK);
    }

    // get a kyc resource
    public function show($id)
    {
        $kyc = KnowCustomer::with('user','country:id,name','nokcountry:id,name',
        'state:id,name','nokstate:id,name')->find($id);

        if (!$kyc) {
            return response(['message' => 'KYC not found'], Response::HTTP_NOT_FOUND);
        }

        return response($kyc, Response::HTTP_OK);
    }

    // delete a kyc resource
    public function destroy($id)
    {
        Gate::authorize('view', 'users');
        
        $kyc = KnowCustomer::find($id);

        if (!$kyc) {
            return response(['message' => 'KYC not found'], Response::HTTP_NOT_FOUND);
        }

        $kyc->delete();

        return response(['message' => 'KYC deleted successfully'], Response::HTTP_ACCEPTED);
    }

    // approve a kyc resource
    public function approveKYC($id)
    {
        $loan = KnowCustomer::find($id);
        
        if (!$loan) {
            return response(['message' => 'KYC not found']);
        }

        $loan->update(['status' => 'successful']);

        return response(['message' => 'KYC Approved'], Response::HTTP_OK);
    }

    // reject a kyc resource
    public function rejectKYC($id)
    {
        $loan = KnowCustomer::find($id);
        
        if (!$loan) {
            return response(['message' => 'KYC not found'], Response::HTTP_NOT_FOUND);
        }

        $loan->update(['status' => 'failed']);

        return response(['message' => 'KYC rejected!'], Response::HTTP_OK);
    }

    // if a kyc has been rejected but due to an appeal from the user to review the kyc rejection,
    // the admin can recycle the kyc so it can be worked on again, it will go to te pending bucket
    public function recycleKYC($id)
    {
        $loan = KnowCustomer::find($id);
        
        if (!$loan) {
            return response(['message' => 'KYC not found'], Response::HTTP_NOT_FOUND);
        }

        $loan->update(['status' => 'pending']);

        return response(['message' => 'KYC recycled, check pending bucket!'], Response::HTTP_OK);
    }

    // used in filtering status such as pending, successful etc
    // parameter used is $status which can be set to any of status
    public function getStatus($status)
    {
        Gate::authorize('view', 'users');

        $loan = KnowCustomer::where("status", $status)->with('user')->get();

        return response($loan, Response::HTTP_OK);
    }

    public function getMyKYC()
    {
        $kyc = KnowCustomer::where('user_id', auth()->user()->id)
            ->with('user','country','nokcountry','state','nokstate')
            ->first();

        return response($kyc, Response::HTTP_OK);
    }
}