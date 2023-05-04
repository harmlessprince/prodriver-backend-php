<?php

namespace App\Console\Commands;

use App\Models\Trip;
use Illuminate\Console\Command;

class UpdateTripTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:trips';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $trips = Trip::query()->where('id', '>', 3700)->where('total_gtv', null)->with(Trip::RELATIONS)->get();
        foreach ($trips as $key => $value) {
            /** @var Trip $trip */
            $trip = $value;
            $trip->total_gtv = $trip->order->amount_willing_to_pay;
            $trip->net_margin_profit_amount =  $trip->order->amount_willing_to_pay -  $trip->total_payout;
            $trip->save();
            
        }
        return Command::SUCCESS;
    }
}
