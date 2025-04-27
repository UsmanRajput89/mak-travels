<?php

namespace App\Services;

use App\Repositories\DealRepository;

class DealService
{
    protected $dealRepository;

    public function __construct(DealRepository $dealRepository)
    {
        $this->dealRepository = $dealRepository;
    }

    public function all()
    {
        return $this->dealRepository->all();
    }
}
