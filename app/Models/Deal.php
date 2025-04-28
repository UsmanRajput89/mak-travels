<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    /** @use HasFactory<\Database\Factories\DealFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'origin',
        'destination',
        'price',
        'currency',
        'departure_date',
        'return_date',
        'provider',
        'deal_type',
        'deal_details',
        'hotel_name',
        'hotel_location',
        'flight_number',
        'created_at',
        'updated_at'
    ];

    public function usersWhoBookmarked()
    {
        return $this->belongsToMany(User::class, 'bookmark');
    }
}
