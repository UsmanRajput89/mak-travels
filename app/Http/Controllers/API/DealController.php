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
    public function getDealById($id)
    {
        $deal = $this->dealService->getDealById($id);

        if (!$deal) {
            return response()->json(['message' => 'Deal not found'], 404);
        }

        return response()->json($deal);
    }

    public function bookmarkDeal($dealId)
    {
        $deal = $this->dealService->bookmarkDeal(auth()->user(), $dealId);

        if (!$deal) {
            return response()->json(['message' => 'Deal not found'], 404);
        }

        return response()->json(['message' => 'Deal bookmarked successfully'], 200);
    }

    public function unbookmarkDeal($dealId)
    {
        $deal = $this->dealService->unbookmarkDeal(auth()->user(), $dealId);

        if (!$deal) {
            return response()->json(['message' => 'Deal not found'], 404);
        }

        return response()->json(['message' => 'Deal unbookmarked successfully'], 200);
    }

    public function getBookmarkedDeals()
    {
        $bookmarkedDeals = $this->dealService->getBookmarkedDeals(auth()->user());

        return response()->json($bookmarkedDeals);
    }
}
