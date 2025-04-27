<?php

namespace App\Repositories;

use App\Models\Deal;

class DealRepository
{
    public function all()
    {
        return Deal::all();
    }
}
