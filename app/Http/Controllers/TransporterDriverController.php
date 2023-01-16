<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TransporterDriverController extends Controller
{
    public function index(Request $request, User $transporter)
    {
        if ($transporter->user_type !== User::USER_TYPE_TRANSPORTER) {
           return $this->respondSuccess([], 'Invalid transporter id supplied');
        }
        $drivers = $transporter->drivers;
        return $this->respondSuccess(['drivers' => $drivers], 'Transporter drivers fetched successfully');
    }
}
