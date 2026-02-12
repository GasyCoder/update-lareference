<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nettoyer les tables pour éviter les doublons
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('role_permissions')->truncate();
        \DB::table('permissions')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Définir toutes les permissions
        $permissions = [
            // Prescriptions
            [
                'name' => 'prescriptions.view',
                'label' => 'Voir les prescriptions',
                'category' => 'prescriptions',
                'description' => 'Permet de consulter la liste des prescriptions'
            ],
            [
                'name' => 'prescriptions.create',
                'label' => 'Créer des prescriptions',
                'category' => 'prescriptions',
                'description' => 'Permet de créer de nouvelles prescriptions'
            ],
            [
                'name' => 'prescriptions.edit',
                'label' => 'Modifier des prescriptions',
                'category' => 'prescriptions',
                'description' => 'Permet de modifier les prescriptions existantes'
            ],
            [
                'name' => 'prescriptions.delete',
                'label' => 'Supprimer des prescriptions',
                'category' => 'prescriptions',
                'description' => 'Permet de supprimer des prescriptions'
            ],

            // Analyses
            [
                'name' => 'analyses.view',
                'label' => 'Voir les analyses',
                'category' => 'analyses',
                'description' => 'Permet de consulter les analyses'
            ],
            [
                'name' => 'analyses.perform',
                'label' => 'Effectuer des analyses',
                'category' => 'analyses',
                'description' => 'Permet de réaliser des analyses'
            ],
            [
                'name' => 'analyses.validate',
                'label' => 'Valider les résultats',
                'category' => 'analyses',
                'description' => 'Permet de valider les résultats d\'analyses'
            ],

            // Patients
            [
                'name' => 'patients.view',
                'label' => 'Voir les patients',
                'category' => 'patients',
                'description' => 'Permet de consulter la liste des patients'
            ],
            [
                'name' => 'patients.manage',
                'label' => 'Gérer les patients',
                'category' => 'patients',
                'description' => 'Permet de créer, modifier et supprimer des patients'
            ],

            // Prescripteurs
            [
                'name' => 'prescripteurs.view',
                'label' => 'Voir les prescripteurs',
                'category' => 'prescripteurs',
                'description' => 'Permet de consulter la liste des prescripteurs'
            ],
            [
                'name' => 'prescripteurs.manage',
                'label' => 'Gérer les prescripteurs',
                'category' => 'prescripteurs',
                'description' => 'Permet de créer, modifier et supprimer des prescripteurs'
            ],

            // Laboratoire
            [
                'name' => 'laboratory.manage',
                'label' => 'Gérer le laboratoire',
                'category' => 'laboratory',
                'description' => 'Permet de gérer les examens, types d\'analyses, prélèvements, etc.'
            ],

            // Administration
            [
                'name' => 'users.manage',
                'label' => 'Gérer les utilisateurs',
                'category' => 'administration',
                'description' => 'Permet de créer, modifier et supprimer des utilisateurs'
            ],
            [
                'name' => 'settings.manage',
                'label' => 'Gérer les paramètres',
                'category' => 'administration',
                'description' => 'Permet de modifier les paramètres du système'
            ],
            [
                'name' => 'trash.access',
                'label' => 'Accéder à la corbeille',
                'category' => 'administration',
                'description' => 'Permet d\'accéder à la corbeille et restaurer/supprimer des éléments'
            ],

            // Archives
            [
                'name' => 'archives.access',
                'label' => 'Accéder aux archives',
                'category' => 'archives',
                'description' => 'Permet de consulter les archives'
            ],
        ];

        // Créer toutes les permissions
        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Assigner les permissions par défaut selon les rôles
        $this->assignDefaultPermissions();
    }

    /**
     * Assigne les permissions par défaut par rôle dans la table role_permissions
     */
    private function assignDefaultPermissions()
    {
        $rolePermissions = [
            'secretaire' => [
                'prescriptions.view',
                'prescriptions.create',
                'prescriptions.edit',
                'patients.view',
                'patients.manage',
                'prescripteurs.view',
                'prescripteurs.manage',
                'archives.access',
            ],
            'technicien' => [
                'analyses.view',
                'analyses.perform',
                'laboratory.manage',
                'archives.access',
            ],
            'biologiste' => [
                'analyses.view',
                'analyses.validate',
                'laboratory.manage',
                'archives.access',
            ],
        ];

        foreach ($rolePermissions as $role => $permissionNames) {
            foreach ($permissionNames as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission) {
                    \DB::table('role_permissions')->insert([
                        'role' => $role,
                        'permission_id' => $permission->id,
                        'granted' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
