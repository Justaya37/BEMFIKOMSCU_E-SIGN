<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->string('qr_position', 30)->default('bottom_left')->after('stamp_position');
            $table->decimal('cap_x', 6, 2)->default(130.00)->after('qr_position');
            $table->decimal('cap_y', 6, 2)->default(200.00)->after('cap_x');
            $table->decimal('cap_width', 6, 2)->default(38.00)->after('cap_y');
            $table->boolean('include_signature_img')->default(false)->after('cap_width');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->dropColumn([
                'qr_position',
                'cap_x',
                'cap_y',
                'cap_width',
                'include_signature_img',
            ]);
        });
    }
};
