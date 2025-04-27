<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Services\DealService as TravelDealService;

class DealController extends Controller
{
    protected $dealService;

    public function __construct(TravelDealService $dealService)
    {
        $this->dealService = $dealService;
    }

    public function allDeals()
    {
        $deals = $this->dealService->all();

        return response()->json([
            "status" => 1,
            "message" => "All Deals",
            "data" => $deals,
        ], 200);
    }
}
