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
        Schema::table('prescriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('prescriptions', 'technicien_id')) {
                $table->foreignId('technicien_id')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('prescriptions', 'date_debut_traitement')) {
                $table->timestamp('date_debut_traitement')->nullable();
            }
            if (!Schema::hasColumn('prescriptions', 'date_reprise_traitement')) {
                $table->timestamp('date_reprise_traitement')->nullable();
            }
        });

        Schema::table('prescription_analyse', function (Blueprint $table) {
            if (!Schema::hasColumn('prescription_analyse', 'is_payer')) {
                $table->string('is_payer')->default('NON_PAYE');
            }
            if (!Schema::hasColumn('prescription_analyse', 'prix')) {
                $table->decimal('prix', 15, 2)->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropForeign(['technicien_id']);
            $table->dropColumn(['technicien_id', 'date_debut_traitement', 'date_reprise_traitement']);
        });

        Schema::table('prescription_analyse', function (Blueprint $table) {
            $table->dropColumn(['is_payer', 'prix']);
        });
    }
};
