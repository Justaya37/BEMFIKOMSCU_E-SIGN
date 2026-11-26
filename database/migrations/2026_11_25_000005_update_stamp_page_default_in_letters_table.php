<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update any existing negative or zero stamp_page values to 1
        DB::table('letters')
            ->where('stamp_page', '<=', 0)
            ->update(['stamp_page' => 1]);

        // 2. Change column default to 1
        Schema::table('letters', function (Blueprint $table) {
            $table->integer('stamp_page')->default(1)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->integer('stamp_page')->default(1)->change();
        });
    }
};
