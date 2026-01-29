{{-- resources/views/livewire/admin/gestion-analyses/_filtres.blade.php --}}

<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg mb-4 transition-colors duration-200">
    <div class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
            {{-- Recherche --}}
            <div class="col-span-1 md:col-span-2 lg:col-span-2">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Recherche</label>
                <div class="flex">
                    <span
                        class="inline-flex items-center px-3 text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 border border-r-0 border-gray-300 dark:border-gray-600 rounded-l-md">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-300 dark:focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 focus:ring-opacity-50 text-sm transition-colors duration-200"
                        placeholder="Référence, patient, prescripteur...">
                    @if($search)
                        <button wire:click="clearSearch"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-r-md hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:focus:ring-gray-400 focus:ring-offset-2 transition-all duration-150"
                            type="button">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Filtre Date --}}
            <div class="col-span-1">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Période</label>
                <select wire:model.live="dateFilter"
                    class="form-select block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-300 dark:focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 focus:ring-opacity-50 text-sm transition-colors duration-200">
                    <option value="">Toutes les dates</option>
                    <option value="today">Aujourd'hui</option>
                    <option value="yesterday">Hier</option>
                    <option value="this_week">Cette semaine</option>
                    <option value="this_month">Ce mois</option>
                    <option value="custom">Personnalisé</option>
                </select>
            </div>

            {{-- Dates personnalisées --}}
            @if($dateFilter === 'custom')
                <div class="col-span-1">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Du</label>
                    <input type="date" wire:model.live="dateDebut"
                        class="form-input block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-300 dark:focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 focus:ring-opacity-50 text-sm transition-colors duration-200">
                </div>
                <div class="col-span-1">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Au</label>
                    <input type="date" wire:model.live="dateFin"
                        class="form-input block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-300 dark:focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 focus:ring-opacity-50 text-sm transition-colors duration-200">
                </div>
            @endif

            {{-- Bouton Filtres Avancés --}}
            <div class="col-span-1">
                <button wire:click="toggleAdvancedFilters"
                    class="w-full px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-150">
                    <i class="fas fa-filter mr-1"></i>
                    {{ $showAdvancedFilters ? 'Masquer' : 'Plus de filtres' }}
                </button>
            </div>

            {{-- Reset --}}
            <div class="col-span-1">
                <button wire:click="clearFilters"
                    class="w-full px-4 py-2 text-sm font-medium text-red-700 dark:text-red-400 bg-white dark:bg-gray-700 border border-red-300 dark:border-red-600 rounded-md shadow-sm hover:bg-red-50 dark:hover:bg-red-900/20 focus:outline-none focus:ring-2 focus:ring-red-500 dark:focus:ring-red-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-150">
                    <i class="fas fa-eraser mr-1"></i> Réinitialiser
                </button>
            </div>
        </div>

        {{-- Filtres Avancés --}}
        @if($showAdvancedFilters)
            <hr class="my-4 border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="col-span-1">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Prescripteur</label>
                    <select wire:model.live="prescripteurFilter"
                        class="form-select block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-300 dark:focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 focus:ring-opacity-50 text-sm transition-colors duration-200">
                        <option value="">Tous les prescripteurs</option>
                        @foreach($this->prescripteurs as $prescripteur)
                            <option value="{{ $prescripteur->id }}">
                                Dr. {{ $prescripteur->nom }} {{ $prescripteur->prenom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-1">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Statut Paiement</label>
                    <select wire:model.live="paymentStatusFilter"
                        class="form-select block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-300 dark:focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 focus:ring-opacity-50 text-sm transition-colors duration-200">
                        <option value="">Tous</option>
                        <option value="paid">Payé</option>
                        <option value="unpaid">Non payé</option>
                    </select>
                </div>
                <div class="col-span-1">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Technicien</label>
                    <select wire:model.live="technicienFilter"
                        class="form-select block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-300 dark:focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 focus:ring-opacity-50 text-sm transition-colors duration-200">
                        <option value="">Tous les techniciens</option>
                        @foreach($this->techniciens as $technicien)
                            <option value="{{ $technicien->id }}">{{ $technicien->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif
    </div>
</div>