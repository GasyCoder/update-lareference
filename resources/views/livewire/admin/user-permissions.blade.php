<div>
    <div class="mb-6">
        <h5 class="text-xl font-bold text-slate-700 dark:text-white mb-1">Classification des Permissions par Rôle</h5>
        <p class="text-sm text-slate-500 dark:text-slate-400">Gérez les accès globaux pour chaque type d'utilisateur de manière granulaire.</p>
    </div>

    @if(session()->has('message'))
        <div class="mb-4 p-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 flex items-center animate__animated animate__fadeIn">
            <em class="ni ni-check-circle-fill text-emerald-500 text-lg me-3"></em>
            <span class="text-emerald-700 dark:text-emerald-400 font-medium">{{ session('message') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($availableRoles as $type => $label)
            <div class="bg-white dark:bg-gray-950 rounded-xl shadow-sm border border-gray-200 dark:border-gray-900 overflow-hidden transition-all duration-300 hover:shadow-md">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-900 bg-gray-50/50 dark:bg-gray-900/50 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                            <em class="ni ni-user-fill text-primary-500 text-xl"></em>
                        </div>
                        <div>
                            <h6 class="font-bold text-slate-700 dark:text-white leading-tight">{{ $label }}</h6>
                            <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Rôle Utilisateur</span>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="space-y-8">
                        @foreach($permissionsByCategory as $category => $permissions)
                            <div>
                                <h3 class="text-xs font-bold text-primary-500 uppercase tracking-widest mb-4 flex items-center">
                                    <span class="bg-primary-500/10 h-1 w-6 rounded-full me-2"></span>
                                    {{ $category }}
                                </h3>
                                
                                <div class="space-y-4 ps-2">
                                    @foreach($permissions as $permission)
                                        <div class="flex items-start justify-between group">
                                            <div class="flex-grow pr-4">
                                                <label for="perm-{{ $type }}-{{ $permission->id }}" class="block text-sm font-semibold text-slate-600 dark:text-slate-300 group-hover:text-primary-500 transition-colors cursor-pointer">
                                                    {{ $permission->label }}
                                                </label>
                                                @if($permission->description)
                                                    <p class="text-xs text-slate-400 dark:text-slate-500 leading-normal mt-0.5 line-clamp-2">
                                                        {{ $permission->description }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="flex-shrink-0 pt-0.5">
                                                <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                                    <input type="checkbox" 
                                                           id="perm-{{ $type }}-{{ $permission->id }}"
                                                           wire:click="togglePermission('{{ $type }}', {{ $permission->id }})"
                                                           {{ isset($rolePermissions[$type][$permission->id]) && $rolePermissions[$type][$permission->id] ? 'checked' : '' }}
                                                           class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-4 border-gray-300 appearance-none cursor-pointer transition-all duration-300 checked:right-0 checked:border-primary-500 focus:outline-none"/>
                                                    <label for="perm-{{ $type }}-{{ $permission->id }}" 
                                                           class="toggle-label block overflow-hidden h-5 rounded-full bg-gray-300 cursor-pointer transition-colors duration-300"></label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <style>
        .toggle-checkbox:checked {
            right: 0;
            border-color: #3b82f6; /* primary-500 equivalent */
        }
        .toggle-checkbox:checked + .toggle-label {
            background-color: #3b82f6; /* primary-500 equivalent */
        }
        .toggle-checkbox {
            right: 1.25rem;
        }
        .toggle-label {
            width: 2.5rem;
        }
    </style>
</div>