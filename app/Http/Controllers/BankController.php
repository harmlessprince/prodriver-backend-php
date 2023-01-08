<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index(): JsonResponse
    {
        return $this->respondSuccess(['banks' => Bank::all()], 'All banks');
    }
}
