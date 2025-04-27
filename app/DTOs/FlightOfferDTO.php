<?php

namespace App\DTOs;

class FlightOfferDTO
{
    public string $origin;
    public string $destination;
    public string $departureDate;
    public string $returnDate;
    public float $price;
    public string $currency;
    public string $provider;

    public function __construct(array $data)
    {
        $this->origin = $data['origin'];
        $this->destination = $data['destination'];
        $this->departureDate = $data['departureDate'];
        $this->returnDate = $data['returnDate'];
        $this->price = $data['price'];
        $this->currency = $data['currency'];
        $this->provider = $data['provider'];
    }
}
