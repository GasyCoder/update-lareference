{{-- resources/views/livewire/admin/gestion-analyses/_stats-cards.blade.php --}}

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-4">
    {{-- Total --}}
    <div class="col-span-1">
        <div
            class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 h-full transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h6 class="text-gray-500 dark:text-gray-400 text-sm mb-1">Total</h6>
                    <h3 class="text-2xl font-bold mb-0 text-gray-900 dark:text-white">
                        {{ number_format($stats['total']) }}
                    </h3>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-full p-3">
                    <i class="fas fa-file-medical text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- En Attente --}}
    <div class="col-span-1">
        <div
            class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 h-full border-l-4 border-yellow-500 dark:border-yellow-400 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h6 class="text-gray-500 dark:text-gray-400 text-sm mb-1">En attente</h6>
                    <h3 class="text-2xl font-bold mb-0 text-yellow-600 dark:text-yellow-400">
                        {{ number_format($stats['en_attente']) }}
                    </h3>
                </div>
                <div class="bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-300 rounded-full p-3">
                    <i class="fas fa-clock text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- En Cours --}}
    <div class="col-span-1">
        <div
            class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 h-full border-l-4 border-blue-400 dark:border-blue-300 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h6 class="text-gray-500 dark:text-gray-400 text-sm mb-1">En cours</h6>
                    <h3 class="text-2xl font-bold mb-0 text-blue-500 dark:text-blue-400">
                        {{ number_format($stats['en_cours']) }}
                    </h3>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900 text-blue-500 dark:text-blue-300 rounded-full p-3">
                    <i class="fas fa-spinner text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Terminées --}}
    <div class="col-span-1">
        <div
            class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 h-full border-l-4 border-blue-600 dark:border-blue-400 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h6 class="text-gray-500 dark:text-gray-400 text-sm mb-1">Terminées</h6>
                    <h3 class="text-2xl font-bold mb-0 text-blue-700 dark:text-blue-300">
                        {{ number_format($stats['termine']) }}
                    </h3>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-full p-3">
                    <i class="fas fa-check text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Validées --}}
    <div class="col-span-1">
        <div
            class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 h-full border-l-4 border-green-500 dark:border-green-400 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h6 class="text-gray-500 dark:text-gray-400 text-sm mb-1">Validées</h6>
                    <h3 class="text-2xl font-bold mb-0 text-green-600 dark:text-green-400">
                        {{ number_format($stats['validees']) }}
                    </h3>
                </div>
                <div class="bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 rounded-full p-3">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- À Refaire --}}
    <div class="col-span-1">
        <div
            class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 h-full border-l-4 border-red-500 dark:border-red-400 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h6 class="text-gray-500 dark:text-gray-400 text-sm mb-1">À refaire</h6>
                    <h3 class="text-2xl font-bold mb-0 text-red-600 dark:text-red-400">
                        {{ number_format($stats['a_refaire']) }}
                    </h3>
                </div>
                <div class="bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300 rounded-full p-3">
                    <i class="fas fa-redo text-xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Taux de performance --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    <div class="col-span-1">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-500 dark:text-gray-400">Taux de complétion</span>
                <span class="font-bold text-gray-900 dark:text-white">{{ $stats['taux_completion'] }}%</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="bg-green-500 dark:bg-green-400 h-2 rounded-full transition-all duration-500"
                    style="width: {{ $stats['taux_completion'] }}%"></div>
            </div>
        </div>
    </div>
    <div class="col-span-1">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 transition-all duration-200 hover:shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-500 dark:text-gray-400">Taux de validation</span>
                <span class="font-bold text-gray-900 dark:text-white">{{ $stats['taux_validation'] }}%</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="bg-blue-600 dark:bg-blue-400 h-2 rounded-full transition-all duration-500"
                    style="width: {{ $stats['taux_validation'] }}%"></div>
            </div>
        </div>
    </div>
</div>