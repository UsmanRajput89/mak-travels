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
    public function getDealById($id)
    {
        return $this->dealRepository->find($id);
    }

    public function bookmarkDeal($user, $dealId)
    {
        return $this->dealRepository->attachBookmark($user, $dealId);
    }

    public function unbookmarkDeal($user, $dealId)
    {
        return $this->dealRepository->detachBookmark($user, $dealId);
    }

    public function getBookmarkedDeals($user)
    {
        return $this->dealRepository->getBookmarkedDeals($user);
    }
}
