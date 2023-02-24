<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\TripDto;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\Truck;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct(private readonly AnalyticsService $analyticsService)
    {
    }
    public function adminStats(Request $request)
    {
        $truckQuery = Truck::query();
        $tripQuery = Trip::query();
        $driverQuery = Driver::query();

        $totalTrips = $this->analyticsService->totalNumberOfTrips($tripQuery);



        return $this->respondSuccess([
            'totalNumberOfTrips' => $this->analyticsService->totalNumberOfTrips(clone $tripQuery),
            'totalNumberOfOngoingTrips' => $this->analyticsService->totalNumberOfOngoingTrips(clone $tripQuery),
            'totalNumberOfFlaggedTrips' => $this->analyticsService->totalNumberOfFlaggedTrips(clone  $tripQuery),
            'totalNumberOfDeliveredTrips' => $this->analyticsService->totalNumberOfDeliveredTrips(clone $tripQuery),
            'totalAmountOfIncome' => "NGN " . number_format($this->analyticsService->totalAmountOfIncome(clone $tripQuery), 2),
            'totalNumberOfCompletedTrips' => $this->analyticsService->totalNumberOfCompletedTrips(clone $tripQuery),
            'totalNumberOfCancelledTrips' => $this->analyticsService->totalNumberOfCancelledTrips(clone $tripQuery),
            'totalPayout' => "NGN " . number_format( $this->analyticsService->totalPayout(clone $tripQuery), 2),
            'totalIncomeForTheMonth' => "NGN " . number_format($this->analyticsService->totalIncomeForTheMonth(clone $tripQuery), 2),
            'totalNumberOfDrivers' => $this->analyticsService->totalNumberOfDrivers(clone $driverQuery),
            'totalAmountOfPendingIncome' => "NGN " . number_format( $this->analyticsService->totalAmountOfPendingIncome(clone $tripQuery), 2),
            'totalNumberOfTrucks' => $this->analyticsService->totalNumberOfTrucks(clone $truckQuery),
        ]);
    }
}
