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
        $user = $request->user();
        $truckQuery = Truck::query();
        $tripQuery = Trip::query();
        $driverQuery = Driver::query();

        return $this->respondSuccess([
            'totalAmountPayable' => "NGN " .  number_format($this->analyticsService->totalAmountPayable(clone $tripQuery), 2),
            'totalNumberOfTrips' => $this->analyticsService->totalNumberOfTrips(clone $tripQuery, $user),
            'totalNumberOfOngoingTrips' => $this->analyticsService->totalNumberOfOngoingTrips(clone $tripQuery, $user),
            'totalNumberOfFlaggedTrips' => $this->analyticsService->totalNumberOfFlaggedTrips(clone  $tripQuery, $user),
            'totalNumberOfDeliveredTrips' => $this->analyticsService->totalNumberOfDeliveredTrips(clone $tripQuery),
            'totalAmountOfIncome' => "NGN " . number_format($this->analyticsService->totalAmountOfIncome(clone $tripQuery, $user), 2),
            'totalNumberOfCompletedTrips' => $this->analyticsService->totalNumberOfCompletedTrips(clone $tripQuery, $user),
            'totalNumberOfCancelledTrips' => $this->analyticsService->totalNumberOfCancelledTrips(clone $tripQuery, $user),
            'totalPayout' => "NGN " . number_format($this->analyticsService->totalPayout(clone $tripQuery), 2),
            'totalIncomeForTheMonth' => "NGN " . number_format($this->analyticsService->totalIncomeForTheMonth(clone $tripQuery, $user), 2),
            'totalNumberOfDrivers' => $this->analyticsService->totalNumberOfDrivers(clone $driverQuery, $user),
            'totalAmountOfPendingIncome' => "NGN " . number_format($this->analyticsService->totalAmountOfPendingIncome(clone $tripQuery, $user), 2),
            'totalNumberOfTrucks' => $this->analyticsService->totalNumberOfTrucks(clone $truckQuery, $user),
            'totalLoadingTonnage' => number_format($this->analyticsService->totalLoadingTonnage(clone $tripQuery), 2),
            'totalMarginProfit' => "NGN " . number_format($this->analyticsService->totalMarginProfit(clone $tripQuery), 2),
            'totalNetMarginProfit' => "NGN " . number_format($this->analyticsService->totalNetMarginProfit(clone $tripQuery), 2),
            'totalNumberOfDivertedTrips' => $this->analyticsService->totalNumberOfDivertedTrips(clone $tripQuery, $user),
            'totalNumberOfAccidentsTrips' => $this->analyticsService->totalNumberOfAccidentsTrips(clone $tripQuery, $user),
        ]);
    }

    public function transporterStats(Request $request)
    {
        $user = $request->user();
        $truckQuery = Truck::query();
        $tripQuery = Trip::query();
        $driverQuery = Driver::query();
        return $this->respondSuccess([
            'totalNumberOfTrips' => $this->analyticsService->totalNumberOfTrips(clone $tripQuery, $user),
            'totalNumberOfOngoingTrips' => $this->analyticsService->totalNumberOfOngoingTrips(clone $tripQuery, $user),
            'totalNumberOfFlaggedTrips' => $this->analyticsService->totalNumberOfFlaggedTrips(clone  $tripQuery, $user),
            'totalAmountOfIncome' => "NGN " . number_format($this->analyticsService->totalAmountOfIncome(clone $tripQuery, $user), 2),
            'totalNumberOfCompletedTrips' => $this->analyticsService->totalNumberOfCompletedTrips(clone $tripQuery, $user),
            'totalNumberOfCancelledTrips' => $this->analyticsService->totalNumberOfCancelledTrips(clone $tripQuery, $user),
            'totalIncomeForTheMonth' => "NGN " . number_format($this->analyticsService->totalIncomeForTheMonth(clone $tripQuery, $user), 2),
            'totalNumberOfDrivers' => $this->analyticsService->totalNumberOfDrivers(clone $driverQuery, $user),
            'totalAmountOfPendingIncome' => "NGN " . number_format($this->analyticsService->totalAmountOfPendingIncome(clone $tripQuery, $user), 2),
            'totalNumberOfTrucks' => $this->analyticsService->totalNumberOfTrucks(clone $truckQuery, $user),
        ]);
    }
    public function cargoOwnerStats(Request $request)
    {
        $user = $request->user();
        $tripQuery = Trip::query();

        return $this->respondSuccess([
            'totalNumberOfTrips' => $this->analyticsService->totalNumberOfTrips(clone $tripQuery, $user),
            'totalNumberOfOngoingTrips' => $this->analyticsService->totalNumberOfOngoingTrips(clone $tripQuery, $user),
            'totalNumberOfFlaggedTrips' => $this->analyticsService->totalNumberOfFlaggedTrips(clone  $tripQuery, $user),
            'totalNumberOfCompletedTrips' => $this->analyticsService->totalNumberOfCompletedTrips(clone $tripQuery, $user),
            'totalNumberOfCancelledTrips' => $this->analyticsService->totalNumberOfCancelledTrips(clone $tripQuery, $user),
        ]);
    }
}
