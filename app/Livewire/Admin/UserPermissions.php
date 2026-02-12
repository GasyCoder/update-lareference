<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class UserPermissions extends Component
{
    public $roles = [
        User::TYPE_ADMIN => 'Administrateur',
        User::TYPE_SECRETAIRE => 'Secrétaire',
        User::TYPE_TECHNICIEN => 'Technicien',
        User::TYPE_BIOLOGISTE => 'Biologiste',
    ];
    public $rolePermissions = [];

    public function mount()
    {
        $this->loadAllRolePermissions();
    }

    public function loadAllRolePermissions()
    {
        $this->rolePermissions = DB::table('role_permissions')
            ->get()
            ->groupBy('role')
            ->map(function ($items) {
                return $items->pluck('granted', 'permission_id')->toArray();
            })
            ->toArray();
    }

    public function togglePermission($role, $permissionId)
    {
        $existing = DB::table('role_permissions')
            ->where('role', $role)
            ->where('permission_id', $permissionId)
            ->first();

        if ($existing) {
            DB::table('role_permissions')
                ->where('id', $existing->id)
                ->update([
                    'granted' => !$existing->granted,
                    'updated_at' => now()
                ]);
        } else {
            DB::table('role_permissions')->insert([
                'role' => $role,
                'permission_id' => $permissionId,
                'granted' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->loadAllRolePermissions();
        session()->flash('message', "Permission pour le rôle {$role} mise à jour!");
    }

    public function render()
    {
        $permissionsByCategory = Permission::orderBy('category')->orderBy('label')->get()->groupBy('category');

        return view('livewire.admin.user-permissions', [
            'permissionsByCategory' => $permissionsByCategory,
            'availableRoles' => $this->roles
        ]);
    }
}
