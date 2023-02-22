<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\TripDto;
use App\Models\Trip;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct(private readonly AnalyticsService $analyticsService)
    {
    }
    public function adminStats(Request $request)
    {
        $tripQuery = Trip::query()->first();
       return $this->respondSuccess(['trip'  => TripDto::fromModel($tripQuery)]);
    }
}
