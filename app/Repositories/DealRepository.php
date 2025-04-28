<?php

namespace App\Repositories;

use App\Models\Deal;

class DealRepository
{
    public function all()
    {
        return Deal::select('id', 'price', 'title', 'deal_type', 'created_at')->get();
    }
    public function find($id)
    {
        return Deal::find($id);
    }

    public function attachBookmark($user, $dealId)
    {
        $deal = $this->find($dealId);

        if ($deal) {
            // Attach bookmark
            $user->bookmarkedDeals()->attach($dealId);
        }

        return $deal;
    }

    public function detachBookmark($user, $dealId)
    {
        $deal = $this->find($dealId);

        if ($deal) {
            // Detach bookmark
            $user->bookmarkedDeals()->detach($dealId);
        }

        return $deal;
    }

    public function getBookmarkedDeals($user)
    {
        return $user->bookmarkedDeals()
            ->select('deals.id as deal_id', 'deals.title', 'deals.price', 'deals.deal_type', 'deals.created_at')
            ->get();
    }
}
