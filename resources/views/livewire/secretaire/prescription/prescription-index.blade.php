{{-- livewire/secretaire/prescription/prescription-index.blade.php --}}
<div class="container mx-auto px-4 py-8 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-8 gap-4">
        <div class="flex items-center gap-3">
            <em class="ni ni-list-round text-primary-600 text-xl"></em>
            <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100 tracking-tight">
                Liste des prescriptions
            </h1>
        </div>
    </div>

    {{-- Dashboard avec statistiques --}}
    @include('livewire.secretaire.dashboard', ['stats' => $this->stats])

    {{-- Barre de recherche + filtres --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6 mb-8">
        <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 flex-wrap">
            {{-- Recherche --}}
            <div class="relative flex-1 min-w-[250px] max-w-lg">
                <em class="ni ni-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></em>
                <input type="text"
                    wire:model.live.debounce.500ms="search"
                    placeholder="Rechercher..."
                    class="w-full pl-10 pr-10 py-3 border border-gray-300 dark:border-slate-600 rounded-lg 
                           bg-white dark:bg-slate-700 text-slate-900 dark:text-slate-100
                           focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                @if($search)
                    <button wire:click="clearSearch"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <em class="ni ni-cross"></em>
                    </button>
                @endif
            </div>

            {{-- Filtres --}}
            <div class="flex flex-wrap items-center gap-2">
                <button wire:click="filterByPaymentStatus('tous')"
                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium transition-all
                           {{ !$paymentFilter ? 'bg-blue-100 text-blue-700 ring-2 ring-blue-500' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <em class="ni ni-list mr-1.5"></em>
                    Toutes
                </button>

                <button wire:click="filterByPaymentStatus('paye')"
                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium transition-all
                           {{ $paymentFilter === 'paye' ? 'bg-emerald-100 text-emerald-700 ring-2 ring-emerald-500' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <em class="ni ni-check-circle mr-1.5"></em>
                    Payées ({{ $this->stats['countPaye'] }})
                </button>

                <button wire:click="filterByPaymentStatus('non_paye')"
                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium transition-all
                           {{ $paymentFilter === 'non_paye' ? 'bg-red-100 text-red-700 ring-2 ring-red-500' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <em class="ni ni-alert-circle mr-1.5"></em>
                    Non Payées ({{ $this->stats['countNonPaye'] }})
                </button>

                @if($paymentFilter)
                    <button wire:click="clearPaymentFilter"
                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-white border border-red-300 text-red-600 hover:bg-red-50">
                        <em class="ni ni-cross mr-1.5"></em>
                        Effacer
                    </button>
                @endif
            </div>

            {{-- Nouveau bouton --}}
            <div class="flex-shrink-0">
                <a href="{{ route('secretaire.prescription.create') }}" wire:navigate
                    class="inline-flex items-center px-4 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all">
                    <em class="ni ni-plus mr-2"></em>
                    Nouvelle prescription
                </a>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 mb-8">
        <div class="border-b border-gray-200 dark:border-slate-700">
            <nav class="flex space-x-8 px-6">
                <button wire:click="switchTab('actives')"
                        class="relative py-4 px-1 border-b-2 font-medium text-sm transition-colors
                               {{ $tab === 'actives' ? 'border-primary-500 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                    <div class="flex items-center gap-2">
                        <em class="ni ni-list-ul"></em>
                        <span>Actives</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100">
                            {{ $this->stats['countActives'] }}
                        </span>
                    </div>
                </button>

                <button wire:click="switchTab('valide')"
                        class="relative py-4 px-1 border-b-2 font-medium text-sm transition-colors
                               {{ $tab === 'valide' ? 'border-primary-500 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                    <div class="flex items-center gap-2">
                        <em class="ni ni-check-circle"></em>
                        <span>Validées</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100">
                            {{ $this->stats['countValide'] }}
                        </span>
                    </div>
                </button>

                <button wire:click="switchTab('deleted')"
                        class="relative py-4 px-1 border-b-2 font-medium text-sm transition-colors
                               {{ $tab === 'deleted' ? 'border-primary-500 text-primary-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                    <div class="flex items-center gap-2">
                        <em class="ni ni-trash"></em>
                        <span>Corbeille</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100">
                            {{ $this->stats['countDeleted'] }}
                        </span>
                    </div>
                </button>
            </nav>
        </div>
    </div>

    {{-- Tab Content --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 overflow-hidden">
        @if($tab === 'actives')
            @include('livewire.secretaire.prescription.prescription-table', [
                'prescriptions' => $this->activePrescriptions,
                'currentTab' => 'actives'
            ])
        @elseif($tab === 'valide')
            @include('livewire.secretaire.prescription.prescription-table', [
                'prescriptions' => $this->validePrescriptions,
                'currentTab' => 'valide'
            ])
        @elseif($tab === 'deleted')
            @include('livewire.secretaire.prescription.prescription-table', [
                'prescriptions' => $this->deletedPrescriptions,
                'currentTab' => 'deleted'
            ])
        @endif
    </div>

    {{-- Modals --}}
    @include('livewire.secretaire.prescription.modals.action-modal-prescription')
    @include('livewire.secretaire.prescription.modals.confirm-payment')
</div>