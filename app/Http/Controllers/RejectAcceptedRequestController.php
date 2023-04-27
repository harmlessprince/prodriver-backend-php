<?php

namespace App\Http\Controllers;

use App\Models\AcceptedOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RejectAcceptedRequestController extends Controller
{
    //

    public function update(Request $request, AcceptedOrder $acceptedOrder)
    {
        if ($acceptedOrder->approvedBy()->exists()) {
            return $this->respondError('You can not reject a request that has been approved');
        }
        $acceptedOrder->declined_by = $request->user()->id;
        $acceptedOrder->declined_at = Carbon::now();
        $acceptedOrder->cancelled_by = $request->user()->id;
        $acceptedOrder->cancelled_at = Carbon::now();
        $acceptedOrder->save();
        return $this->respondSuccess([], 'Accepted request declined successfully');
    }
}
