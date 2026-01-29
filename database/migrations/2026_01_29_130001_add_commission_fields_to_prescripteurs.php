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
        Schema::table('prescripteurs', function (Blueprint $table) {
            $table->decimal('commission_quota', 15, 2)->default(250000.00)->after('notes');
            $table->decimal('commission_pourcentage', 5, 2)->default(10.00)->after('commission_quota');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescripteurs', function (Blueprint $table) {
            $table->dropColumn(['commission_quota', 'commission_pourcentage']);
        });
    }
};
