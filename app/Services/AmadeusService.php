<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Deal;
use Log;
use Illuminate\Support\Facades\Cache;
use Storage;

class AmadeusService
{
    private $apiKey;
    private $apiSecret;

    public function __construct()
    {
        // You can store these in your .env file for security
        $this->apiKey = env('AMADEUS_API_KEY');
        $this->apiSecret = env('AMADEUS_API_SECRET');
    }

    private function getAccessToken()
    {
        // Check if access_token already cached
        if (Cache::has('amadeus_access_token')) {
            return Cache::get('amadeus_access_token');
        }

        // Otherwise, request new token
        $response = Http::asForm()->post('https://test.api.amadeus.com/v1/security/oauth2/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->apiKey,
            'client_secret' => $this->apiSecret,
        ]);

        $data = $response->json();

        if (isset($data['access_token'])) {
            // Cache token for 25 minutes
            Cache::put('amadeus_access_token', $data['access_token'], now()->addMinutes(25));
            return $data['access_token'];
        } else {
            throw new \Exception('Failed to authenticate with Amadeus: ' . json_encode($data));
        }
    }
    // Fetch Flight Deals from Amadeus API

    public function fetchFlightDeals()
    {
        $accessToken = $this->getAccessToken();

        // 1. Define popular routes
        $popularRoutes = [
            ['origin' => 'JFK', 'destination' => 'LHR'],
            ['origin' => 'LAX', 'destination' => 'CDG'],
            ['origin' => 'SFO', 'destination' => 'DXB'],
            ['origin' => 'ORD', 'destination' => 'FRA'],
            ['origin' => 'DFW', 'destination' => 'HND'],
        ];

        // 2. Pick a random route
        $randomRoute = $popularRoutes[array_rand($popularRoutes)];

        // 3. Generate a random date (today → 6 months ahead)
        $randomDate = now()
            ->addDays(rand(0, 180)) // 0-180 days in future
            ->format('Y-m-d');

        // 4. Fetch deals for the random route/date
        $response = Http::withToken($accessToken)
            ->get('https://test.api.amadeus.com/v2/shopping/flight-offers', [
                'originLocationCode' => $randomRoute['origin'],
                'destinationLocationCode' => $randomRoute['destination'],
                'departureDate' => $randomDate,
                'adults' => 1,
                'max' => 10, // Adjust based on API limits
            ]);

        $this->processFlightOffers($response);
    }

    protected function processFlightOffers($response)
    {
        $flightOffers = $response->json();

        if (!isset($flightOffers['data'])) {
            logger()->error('Flight Deals Fetch Failed', $flightOffers);
            return;
        }

        foreach ($flightOffers['data'] as $offer) {
            try {
                $firstSegment = $offer['itineraries'][0]['segments'][0] ?? null;
                $secondSegment = $offer['itineraries'][0]['segments'][1] ?? null;

                if (!$firstSegment)
                    continue;

                $flightNumber = ($firstSegment['carrierCode'] ?? '') . ($firstSegment['number'] ?? '');

                Deal::updateOrCreate(
                    ['deal_details->id' => $offer['id'] ?? null],
                    [
                        'title' => 'Flight from ' . ($firstSegment['departure']['iataCode'] ?? '') . ' to ' . ($firstSegment['arrival']['iataCode'] ?? ''),
                        'origin' => $firstSegment['departure']['iataCode'] ?? '',
                        'destination' => $firstSegment['arrival']['iataCode'] ?? '',
                        'price' => $offer['price']['grandTotal'] ?? 0,
                        'currency' => $offer['price']['currency'] ?? '',
                        'departure_date' => $firstSegment['departure']['at'] ?? null,
                        'return_date' => $secondSegment['arrival']['at'] ?? null,
                        'provider' => 'Amadeus',
                        'deal_type' => 'flight',
                        'deal_details' => json_encode($offer),
                        'flight_number' => $flightNumber,
                    ]
                );
            } catch (\Exception $e) {
                logger()->error('Error processing flight offer', [
                    'offer' => $offer,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    // Fetch Hotel Deals from Amadeus API
    public function fetchHotelDeals()
    {
        $accessToken = $this->getAccessToken();
        // $hotelListResponse = Http::withToken($accessToken)
        //     ->get('https://test.api.amadeus.com/v1/reference-data/locations/hotels/by-city', [
        //         'cityCode' => 'PAR'
        //     ]);

        $content = file_get_contents(storage_path('app/hotels.json'));
        $hotels = json_decode($content, true);
        if (count($hotels) < 5) {
            throw new \Exception("Not enough hotels available to fetch deals.");
        }
        $selectedHotels = [];
        for ($i = 0; $i < 5; $i++) {
            $hotel = $hotels[array_rand($hotels)];
            $selectedHotels[] = $hotel;

            $hotels = array_filter($hotels, fn($h) => $h['hotelId'] !== $hotel['hotelId']);
        }
        file_put_contents(storage_path('app/hotels.json'), json_encode(array_values($hotels), JSON_PRETTY_PRINT));


        foreach ($selectedHotels as $hotel) {
            $response = Http::withToken($accessToken)
                ->get('https://test.api.amadeus.com/v3/shopping/hotel-offers', [
                    'hotelIds' => $hotel['hotelId'],
                    'bestRateOnly' => true,
                ]);
            $this->processHotelOffers($hotel, $response);
        }

    }

    public function processHotelOffers($hotel, $response)
    {
        $hotelOffers = $response->json();

        // Check if 'data' exists and has at least one item with 'offers'
        if (isset($hotelOffers['data']) && is_array($hotelOffers['data']) && count($hotelOffers['data']) > 0) {
            foreach ($hotelOffers['data'] as $hotelData) {
                if (isset($hotelData['offers']) && is_array($hotelData['offers'])) {
                    foreach ($hotelData['offers'] as $offer) {
                        Deal::create([
                            'title' => $offer['room']['description']['text'] ?? 'Hotel Deal',
                            'origin' => null,
                            'destination' => null,
                            'price' => $offer['price']['total'] ?? 0,
                            'currency' => $offer['price']['currency'] ?? 'USD',
                            'departure_date' => $offer['checkInDate'] ?? null,
                            'return_date' => $offer['checkOutDate'] ?? null,
                            'provider' => 'amadeus',
                            'deal_type' => 'hotel',
                            'deal_details' => json_encode($offer),
                            'hotel_name' => $hotel['name'] ?? null,
                            'hotel_location' => $hotel['location'] ?? null,
                            'flight_number' => $offer['id'] ?? null,
                        ]);
                    }
                }
            }
        } else {
            Log::warning('No hotel offers found or invalid response for hotel: ' . ($hotel['hotelId'] ?? 'Unknown'));
        }
    }
}
