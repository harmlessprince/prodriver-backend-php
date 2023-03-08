<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Trip;
use App\Models\User;
use App\Models\Order;
use App\Models\Truck;
use App\Models\Driver;
use App\Models\Company;
use App\Models\Tonnage;
use App\Models\TripStatus;
use App\Models\AcceptedOrder;
use App\Models\WaybillStatus;
use App\Services\OrderServices;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\DataTransferObjects\AcceptOrderDto;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use App\DataTransferObjects\ApproveAcceptedOrderDto;
use App\Models\TruckType;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\HasReferencesToOtherSheets;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class TripsDatabaseSheet implements ToArray, HasReferencesToOtherSheets, WithCalculatedFormulas, WithHeadingRow, WithChunkReading, ShouldQueue, SkipsEmptyRows
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
        $anonymousTruck = Truck::where('transporter_id', $anonymousTransporter->id)->first();
        $anonymousDriver = Driver::where('user_id', $anonymousTransporter->id)->first();
        $tripStatusId =  TripStatus::query()->where('name', TripStatus::STATUS_COMPLETED)->first()->id;
        $wayBillStatusId = WaybillStatus::query()->where('name', WaybillStatus::STATUS_RECEIVED)->first()->id;
        $truckType =  TruckType::where('name', 'Regular Truck')->first();

        foreach ($rows as $key => $row) {

            // dd($row);
            $cargoOwner = null;
            $transporter = null;
            $driver = null;
            $truck = null;
            $delivery_status = null;
            try {
                // DB::beginTransaction();
                $trip_id = $row['trip_id'];
                $truck_number = $row['truck_number'] ?? null;
                $loading_site = $row['loading_site'] ?? 'loading site';
                $destination = $row['destination'] ?? 'destination site';
                $loading_date = $row['loading_date'] ? Date::excelToDateTimeObject($row['loading_date']) : Carbon::now();
                $delivery_date = $row['delivery_date'] ? Date::excelToDateTimeObject($row['delivery_date']) : Carbon::now();
                $pay_out_status = strtolower($row['pay_out_status'] ?? 'completed');
                $delivery_status = strtolower($row['delivery_status'] ?? 'completed');
                $company = $row['company'] ?? 'completed';
                $loading_tonnage = $row['loading_tonnage'];
                $client = $row['client'];
                $ref_id = $row['ref_id'];
                $gtv = $row['gtv'];
                $payout_rate = $row['payout_rate'] ?? 0;
                $advance = $row['advance'] ?? 0;
                $balance = $row['balance'];
                $advance_gtv = $row['advance_gtv'] ?? 0;
                $balance_gtv = $row['balance_gtv'] ?? 0;
                $receivable = $row['receivable'];
                $net_margin = $row['net_margin'];

                $payable = $row['payable'];
                $margin = $row['margin'];
                $margin_rate = $row['pm'];
                $driver_name = $row['driver_name'];
                $driver_phone = $row['driver_phone'];
                $goods = $row['goods'];
                $partner = $row['partner'];
                $partner_phone = $row['partner_phone'];

                $loaded_by = $row['loaded_by'];
                $remark = $row['remark'];
                $completed_date =  $row['completed_date'] ? Date::excelToDateTimeObject($row['completed_date']) : Carbon::now();
                $days_delivered = $row['days_delivered'];
                $days_in_transit = $row['days_in_transit'];

                $str = "Importing for id " . $trip_id . "\n";

                // printf($str);
                if (!Trip::where('trip_id', $trip_id)->exists() && $trip_id) {

                    if ($partner) {
                        $name = explode(' ', $partner);
                        $first_name = $name[0] ?? null;
                        $last_name = $name[1] ??  null;
                        $transporter  = User::query()->firstOrCreate(
                            ['phone_number' => $partner_phone],
                            [
                                'first_name' => $first_name,
                                'last_name' => $last_name,
                                'password' => '$2y$10$LIduCnhF4GBYEKkUaptgAOPThAfItENWyFR13uH93A.N7Y8blm/9u',
                                'user_type' => User::USER_TYPE_TRANSPORTER,
                                'email_verified_at' => now(),
                            ]
                        );
                    }
                    if ($client) {
                        $cargoOwner = User::query()->firstOrCreate(
                            ['first_name' => $client],
                            [
                                'last_name' => $client,
                                'password' => '$2y$10$LIduCnhF4GBYEKkUaptgAOPThAfItENWyFR13uH93A.N7Y8blm/9u',
                                'user_type' => User::USER_TYPE_CARGO_OWNER,
                                'email_verified_at' => now(),
                            ]
                        );
                        $cargoOwner->company()->firstOrCreate(['name' => $client]);
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
                                'truck_type_id' => $truckType->id,
                                'driver_id' => $driver ? $driver->id : $anonymousDriver->id,
                                'transporter_id' => $transporter ? $transporter->id : $anonymousTransporter->id,
                            ]
                        );
                    }

                    if ($loading_tonnage) {
                        $tonnage = Tonnage::where('value', '>=', $loading_tonnage)->orderBy('value', 'asc')->first() ??  Tonnage::first();
                    } else {
                        $tonnage = Tonnage::first();
                    }


                    // if ($company) {
                    //     $company = Company::query()->firstOrCreate(['name' => $company]);
                    // }


                    $tripStatusId = TripStatus::where('name', $delivery_status)->first()->id;

                    //create 
                    // create the request
                    $order =  Order::query()->create([
                        'created_by' => $anonymousAdmin->id,
                        'tonnage_id' => $tonnage->id,
                        'amount_willing_to_pay' => $gtv,
                        'potential_payout' => $payable,
                        'status' => 'completed',
                        'financial_status' => $pay_out_status == 'completed' ? 'paid' : 'pending',
                        'date_needed' => $loading_date,
                        'pickup_address' => $loading_site,
                        'destination_address' => $destination,
                        'description' => $goods,
                        'cargo_owner_id' => $cargoOwner ? $cargoOwner->id : $anonymousCargoOwner->id,
                    ]);
                    $order->truckTypes()->sync([$truckType->id]);
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
                        loading_date: $loading_date,
                        delivery_date: $delivery_date,
                    );


                    $approveAcceptedOrderDto->margin_profit_amount = $margin;
                    $approveAcceptedOrderDto->margin_profit_percentage = $margin_rate;
                    $approveAcceptedOrderDto->trip_status_id = $tripStatusId;
                    $approveAcceptedOrderDto->way_bill_status_id = $wayBillStatusId;
                    $approveAcceptedOrderDto->balance_payout = $balance;
                    $approveAcceptedOrderDto->delivery_status = $delivery_status;
                    $approveAcceptedOrderDto->payout_status = $pay_out_status;
                    $approveAcceptedOrderDto->days_delivered = $days_delivered;
                    $approveAcceptedOrderDto->days_in_transit = $days_in_transit;
                    $approveAcceptedOrderDto->completed_date = $completed_date;
                    $approveAcceptedOrderDto->net_margin_profit_amount = $net_margin;
                    $approveAcceptedOrderDto->balance_gtv   = $balance_gtv;
                    $approveAcceptedOrderDto->advance_gtv = $advance_gtv;
                    $approveAcceptedOrderDto->total_gtv = $gtv;


                    $orderService = new OrderServices();
                    $trip = $orderService->convertApprovedOrderToTrip($approveAcceptedOrderDto);
                    if ($loading_tonnage) {
                        $trip->loading_tonnage_value = $loading_tonnage;
                        $trip->update();
                    }

                    // if ($trip->trip_id === null) {
                    //     $trip->trip_id = 'TID' . str_pad($trip->id, 6, "0", STR_PAD_LEFT);

                    //     $trip->update();
                    // }
                    // DB::commit();
                    $str = "Committed for id " . $trip_id . "\n";

                    printf($str);
                } else {
                    $str = "Found for id " . $trip_id . "\n";

                    printf($str);
                }
                // return $trip;
            } catch (\Throwable $th) {
                dd($th);
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
        return 1;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
