<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StateController extends Controller
{
    /**
     * @throws \Exception
     */
    public function index(Request $request): JsonResponse
    {
        if (!$request->has('country_id')) return $this->respondError('Please provide country_id as param');
        $states =  State::query()->where('country_id', $request->country_id)->with('country')->get();
        return $this->respondSuccess(['states' => $states], 'All states fetched');
    }
}
