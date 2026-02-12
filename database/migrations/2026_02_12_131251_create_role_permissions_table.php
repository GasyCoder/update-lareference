<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role'); // admin, secretaire, technicien, biologiste
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->boolean('granted')->default(true);
            $table->timestamps();

            $table->unique(['role', 'permission_id']);
        });

        // Seed default permissions
        $this->seedDefaultPermissions();
    }

    private function seedDefaultPermissions(): void
    {
        $permissions = DB::table('permissions')->get()->pluck('id', 'name')->toArray();

        $defaults = [
            'admin' => [
                'prescriptions.view',
                'prescriptions.create',
                'prescriptions.edit',
                'prescriptions.delete',
                'analyses.view',
                'analyses.perform',
                'analyses.validate',
                'patients.view',
                'patients.manage',
                'prescripteurs.view',
                'prescripteurs.manage',
                'laboratory.manage',
                'users.manage',
                'settings.manage',
                'trash.access',
                'archives.access'
            ],
            'secretaire' => [
                'prescriptions.view',
                'prescriptions.create',
                'prescriptions.edit',
                'patients.view',
                'patients.manage',
                'prescripteurs.view',
                'prescripteurs.manage',
                'archives.access'
            ],
            'technicien' => [
                'analyses.view',
                'analyses.perform',
                'archives.access'
            ],
            'biologiste' => [
                'analyses.view',
                'analyses.validate',
                'archives.access'
            ],
        ];

        foreach ($defaults as $role => $rolePermissions) {
            foreach ($rolePermissions as $permName) {
                if (isset($permissions[$permName])) {
                    DB::table('role_permissions')->insert([
                        'role' => $role,
                        'permission_id' => $permissions[$permName],
                        'granted' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
