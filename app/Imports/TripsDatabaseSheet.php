<?php

namespace App\Imports;

use App\DataTransferObjects\AcceptOrderDto;
use App\DataTransferObjects\ApproveAcceptedOrderDto;
use App\Models\AcceptedOrder;
use App\Models\Company;
use App\Models\Driver;
use App\Models\Order;
use App\Models\Tonnage;
use App\Models\Trip;
use App\Models\TripStatus;
use App\Models\Truck;
use App\Models\User;
use App\Models\WaybillStatus;
use App\Services\OrderServices;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\HasReferencesToOtherSheets;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TripsDatabaseSheet implements ToArray, HasReferencesToOtherSheets, WithCalculatedFormulas, WithHeadingRow, WithChunkReading, ShouldQueue
{
    /**
     * @param Collection $collection
     */
    public function array(array $rows)
    {
        $anonymousAdmin = User::where('first_name', 'Admin')->first();
        $anonymousTransporter = User::where('first_name', 'Truck')->first();
        $anonymousCargoOwner = User::where('first_name', 'Cargo')->first();
        $anonymousAccountManager = User::where('first_name', 'Account')->first();
        $anonymousTruck = Truck::where('truck_owner_id', $anonymousTransporter->id)->first();
        $anonymousDriver = Driver::where('user_id', $anonymousTransporter->id)->first();
        $tripStatusId =  TripStatus::query()->where('name', TripStatus::STATUS_DELIVERED)->first()->id;
        $wayBillStatusId = WaybillStatus::query()->where('name', WaybillStatus::STATUS_RECEIVED)->first()->id;

        foreach ($rows as $key => $row) {
            $cargoOwner = null;
            $transporter = null;
            $driver = null;
            $truck = null;
            try {
                DB::beginTransaction();
                $trip_id = $row['trip_id'];
                $truck_number = $row['truck_number'] ?? null;
                $loading_site = $row['loading_site'] ?? 'loading site';
                $destination = $row['destination'] ?? 'destination site';
                $loading_date = $row['loading_date'] ?? Carbon::now();
                $pymt_status = strtolower($row['pymt_status'] ?? 'completed');
                $company = $row['company'] ?? '';
                $loading_tonnage = $row['loading_tonnage'];
                $client = $row['client'];
                $ref_id = $row['ref_id'];
                $gtv = $row['gtv'];
                $advance = $row['advance'] ?? 0;
                $balance = $row['balance'];
                $receivable = $row['receivable'];
                $payout_rate = $row['payout_rate'] ?? 0;
                $payable = $row['payable'];
                $margin = $row['margin'];
                $margin_rate = $row[22];
                $driver_name = $row['driver_name'];
                $driver_phone = $row['driver_phone'];
                $goods = $row['goods'];
                $partner = $row['partner'];
                $phone = $row['phone'];

                $loaded_by = $row['loaded_by'];
                $remark = $row['remark'];
                $completed_date = $row['completed_date'];
                $days_delivered = $row['days_delivered'];
                $days_in_transit = $row['days_in_transit'];

                if (!Trip::where('trip_id', $trip_id)->exists() && $trip_id) {
                    if ($partner) {
                        $cargoOwner = User::query()->firstOrCreate(
                            ['first_name' => $partner],
                            [
                                'password' => '$2y$10$LIduCnhF4GBYEKkUaptgAOPThAfItENWyFR13uH93A.N7Y8blm/9u',
                                'user_type' => User::USER_TYPE_TRANSPORTER,
                                'email_verified_at' => now(),
                            ]
                        );
                    }
                    if ($client) {
                        $transporter = User::query()->firstOrCreate(
                            ['first_name' => $client],
                            [
                                'password' => '$2y$10$LIduCnhF4GBYEKkUaptgAOPThAfItENWyFR13uH93A.N7Y8blm/9u',
                                'user_type' => User::USER_TYPE_CARGO_OWNER,
                                'email_verified_at' => now(),
                            ]
                        );
                    }
                    if ($driver_name) {
                        $driver =    Driver::query()->firstOrCreate(
                            ['phone_number' => $driver_phone],
                            ['first_name' => $driver_name, 'user_id' => $transporter ? $transporter->id : $anonymousTransporter->id]
                        );
                    }
                    if ($truck_number) {
                        $truck = Truck::query()->firstOrCreate(
                            [
                                'plate_number' => $truck_number,

                            ],
                            [
                                'driver_id' => $driver ? $driver->id : $anonymousDriver->id,
                                'truck_owner_id' => $transporter ? $transporter->id : $anonymousTransporter->id,
                            ]
                        );
                    }

                    if ($loading_tonnage) {
                        $tonnage = Tonnage::where('value', '>=', $loading_tonnage)->orderBy('value', 'asc')->first() ??  Tonnage::first();
                    } else {
                        $tonnage = Tonnage::first();
                    }


                    if ($company) {
                        $company = Company::query()->firstOrCreate(['name' => $company, 'user_id' => $transporter ? $transporter->id : $anonymousTransporter->id]);
                    }

                    //create 
                    // create the request
                    $order =  Order::query()->create([
                        'created_by' => $anonymousAdmin->id,
                        'tonnage_id' => $tonnage->id,
                        'amount_willing_to_pay' => $gtv,
                        'potential_payout' => $payable,
                        'pickup_address' => $loading_site,
                        'destination_address' => $destination,
                        'description' => $goods,
                        'cargo_owner_id' => $cargoOwner ? $cargoOwner->id : $anonymousCargoOwner->id,
                    ]);

                    //match the request
                    $acceptedOrder =   AcceptedOrder::query()->create([
                        'order_id' => $order->id,
                        'truck_id' => $truck ? $truck->id : $anonymousTruck->id,
                        'accepted_by' => $transporter ? $transporter->id : $anonymousTransporter->id,
                        'accepted_at' => Carbon::now(),
                        'approved_by' =>  $anonymousAdmin->id,
                        'approved_at' => Carbon::now(),
                        'matched_by' => $anonymousAdmin->id,
                        'matched_at' => Carbon::now(),
                        'amount' => $payout_rate,
                    ]);



                    $acceptedOrderDto = AcceptOrderDto::fromModel($acceptedOrder);



                    $approveAcceptedOrderDto = new ApproveAcceptedOrderDto(
                        accepted_order_id: $acceptedOrderDto->id,
                        trip_id: $trip_id,
                        driver_id: $driver ? $driver->id : $anonymousDriver->id,
                        truck_id: $truck ? $truck->id : $anonymousTruck->id,
                        order_id: $acceptedOrderDto->order_id,
                        cargo_owner_id: $order->cargo_owner_id,
                        transporter_id: $acceptedOrderDto->accepted_by,
                        account_manager_id: $anonymousAccountManager->id,
                        approved_by: $anonymousAdmin->id,
                        total_payout: $acceptedOrderDto->amount,
                        advance_payout: $advance,
                        loading_date: Carbon::now(),
                        delivery_date: Carbon::now(),
                    );


                    $approveAcceptedOrderDto->margin_profit_amount = $margin;
                    $approveAcceptedOrderDto->margin_profit_percentage = $margin_rate;
                    $approveAcceptedOrderDto->trip_status_id = $tripStatusId;
                    $approveAcceptedOrderDto->way_bill_status_id = $wayBillStatusId;
                    $approveAcceptedOrderDto->balance = $payout_rate && $advance && $payout_rate > 0 ? $payout_rate - $advance : 0;
                    $orderService = new OrderServices();
                    $trip = $orderService->convertApprovedOrderToTrip($approveAcceptedOrderDto);
                    if ($trip->trip_id === null) {
                        $trip->trip_id = 'TID' . str_pad($trip->id, 6, "0", STR_PAD_LEFT);
                        $trip->save();
                    }
                    DB::commit();
                }
                // return $trip;
            } catch (\Throwable $th) {
                Log::error($th);
                Log::info($row);
                DB::rollBack();
                throw $th;
                exit;
            }
        }
    }
    public function headingRow(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
