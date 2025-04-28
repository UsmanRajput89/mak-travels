<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AmadeusService;
class DealsFetchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deals:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch flight and hotel deals from Amadeus API and save them to the database';

    /**
     * Execute the console command.
     */
    public function handle(AmadeusService $amadeusService)
    {
        $this->info('Fetching flight deals...');
        $amadeusService->fetchFlightDeals();

        $this->info('Fetching hotel deals...');
        $amadeusService->fetchHotelDeals();

        $this->info('Deals fetching completed!');
    }
}
