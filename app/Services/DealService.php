<?php

namespace App\Services;

use App\Repositories\DealRepository;

class DealService
{
    protected $dealRepository;
    // protected $amadeusService;
    public function __construct(DealRepository $dealRepository)
    {
        $this->dealRepository = $dealRepository;
        // $this->amadeusService = $amadeusService;
    }

    public function all()
    {
        // $amadeusDeals = $this->amadeusService->searchDeals();
        return $this->dealRepository->all();
    }
}
