{{-- resources/views/livewire/admin/gestion-analyses.blade.php --}}

<div class="container mx-auto px-4 py-4">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-2xl font-semibold mb-1">
                <i class="fas fa-vials mr-2 text-blue-600"></i>
                Gestion des Analyses
            </h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-0">Vue d'ensemble et suivi des prescriptions par statut</p>
        </div>
        <div class="flex space-x-2">
            <button wire:click="refreshStats" class="px-3 py-1 text-sm font-medium text-blue-700 bg-transparent border border-blue-700 rounded-md hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:border-blue-500 dark:text-blue-400 dark:hover:bg-blue-500 dark:hover:text-white dark:focus:ring-blue-400">
                <i class="fas fa-sync-alt mr-1"></i> Rafraîchir
            </button>
            <button wire:click="exportTab" class="px-3 py-1 text-sm font-medium text-green-700 bg-transparent border border-green-700 rounded-md hover:bg-green-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:border-green-500 dark:text-green-400 dark:hover:bg-green-500 dark:hover:text-white dark:focus:ring-green-400">
                <i class="fas fa-file-excel mr-1"></i> Exporter
            </button>
        </div>
    </div>

    {{-- Messages Flash --}}
    @if (session()->has('success'))
        <div class="flex items-center justify-between p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i><span>{{ session('success') }}</span>
            </div>
            <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-green-100 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex h-8 w-8 dark:bg-green-200 dark:text-green-600 dark:hover:bg-green-300" data-bs-dismiss="alert" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="flex items-center justify-between p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i><span>{{ session('error') }}</span>
            </div>
            <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-red-100 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex h-8 w-8 dark:bg-red-200 dark:text-red-600 dark:hover:bg-red-300" data-bs-dismiss="alert" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </button>
        </div>
    @endif

    {{-- Stats Cards --}}
    @include('livewire.admin.gestion-analyses._stats-cards', ['stats' => $this->stats])

    {{-- Filtres --}}
    @include('livewire.admin.gestion-analyses._filtres')

    {{-- Tabs Navigation --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg">
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 rounded-t-lg">
            <ul class="flex space-x-4" role="tablist">
                @foreach($tabs as $tabKey => $tabConfig)
                    <li role="presentation">
                        <button 
                            wire:click="switchTab('{{ $tabKey }}')"
                            class="flex items-center space-x-2 px-4 py-2 text-sm font-medium leading-5 rounded-md focus:outline-none transition ease-in-out duration-150 
                                {{ $activeTab === $tabKey ? 'text-blue-600 bg-blue-50 hover:bg-blue-100' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}"
                            type="button"
                        >
                            <i class="fas fa-{{ $tabConfig['icon'] }} mr-2"></i>
                            <span>{{ $tabConfig['label'] }}</span>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $activeTab === $tabKey ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $this->stats[$tabKey] ?? 0 }}
                            </span>
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="p-0">
            {{-- Contenu des tabs --}}
            @switch($activeTab)
                @case('prescriptions')
                    @include('livewire.admin.gestion-analyses._prescriptions', ['data' => $this->prescriptions])
                    @break
                @case('en_attente')
                    @include('livewire.admin.gestion-analyses._en-attente', ['data' => $this->prescriptionsEnAttente])
                    @break
                @case('en_cours')
                    @include('livewire.admin.gestion-analyses._en-cours', ['data' => $this->prescriptionsEnCours])
                    @break
                @case('termine')
                    @include('livewire.admin.gestion-analyses._terminees', ['data' => $this->prescriptionsTermine])
                    @break
                @case('validees')
                    @include('livewire.admin.gestion-analyses._validees', ['data' => $this->prescriptionsValidees])
                    @break
                @case('a_refaire')
                    @include('livewire.admin.gestion-analyses._a-refaire', ['data' => $this->prescriptionsARefaire])
                    @break
            @endswitch
        </div>
    </div>

    {{-- Modals --}}
    @include('livewire.admin.gestion-analyses._modals')

    @push('scripts')
        <script>
            // Utiliser un flag pour éviter les doubles enregistrements si le script est ré-exécuté
            if (typeof window.adminGestionAnalysesVars === 'undefined') {
                window.adminGestionAnalysesVars = {
                    listenerRegistered: false
                };
            }

            document.addEventListener('livewire:initialized', () => {
                if (!window.adminGestionAnalysesVars.listenerRegistered) {
                    Livewire.on('open-window', (event) => {
                        const data = Array.isArray(event) ? event[0] : event;
                        if (data && data.url) {
                            window.open(data.url, '_blank');
                        }
                    });
                    window.adminGestionAnalysesVars.listenerRegistered = true;
                }
            });
        </script>
    @endpush
</div>
