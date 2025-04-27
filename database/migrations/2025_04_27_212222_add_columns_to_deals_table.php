<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            // Add deal_type to differentiate between flight and hotel deals
            $table->string('deal_type')->nullable();  // e.g., 'flight', 'hotel', etc.

            // Add deal_details to store raw API data (JSON)
            $table->json('deal_details')->nullable();  // Store all details from Amadeus API

            // You can also add any other relevant fields based on Amadeus data
            // For example:
            $table->string('hotel_name')->nullable(); // Only for hotel deals
            $table->string('hotel_location')->nullable(); // Only for hotel deals
            $table->string('flight_number')->nullable(); // Only for flight deals
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            // Remove the columns in case of rollback
            $table->dropColumn('deal_type');
            $table->dropColumn('deal_details');
            $table->dropColumn('hotel_name');
            $table->dropColumn('hotel_location');
            $table->dropColumn('flight_number');
        });
    }
};
