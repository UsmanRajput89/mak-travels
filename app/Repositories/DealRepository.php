<?php

namespace App\Repositories;

use App\Models\Deal;

class DealRepository
{
    public function all()
    {
        return Deal::select('id','price', 'title', 'deal_type', 'created_at')->get();
    }
    public function find($id)
    {
        return Deal::find($id);
    }
}
