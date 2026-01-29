{{-- resources/views/livewire/admin/gestion-analyses/_modals.blade.php --}}

{{-- Modal Détails --}}
@if($showDetailsModal && $this->selectedPrescription)
    <div
        class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 dark:bg-opacity-90 flex items-center justify-center transition-opacity duration-300">
        <div class="relative w-full max-w-4xl mx-auto p-4">
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl max-h-[90vh] flex flex-col transform transition-all duration-300">
                <div
                    class="px-6 py-4 bg-blue-600 dark:bg-blue-700 text-white flex items-center justify-between rounded-t-lg">
                    <h5 class="text-xl font-semibold flex items-center">
                        <i class="fas fa-file-medical mr-2"></i>
                        Détails - {{ $this->selectedPrescription->reference }}
                    </h5>
                    <button type="button"
                        class="text-white hover:text-gray-200 focus:outline-none transition-colors duration-150"
                        wire:click="closeDetailsModal" aria-label="Close">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>
                <div class="p-6 flex-grow overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Informations Patient --}}
                        <div class="col-span-1">
                            <div
                                class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-sm h-full">
                                <div
                                    class="px-4 py-2 bg-gray-50 dark:bg-gray-600 border-b border-gray-200 dark:border-gray-500 rounded-t-lg flex items-center">
                                    <i class="fas fa-user mr-2 text-gray-700 dark:text-gray-300"></i>
                                    <span class="font-medium text-gray-900 dark:text-white">Patient</span>
                                </div>
                                <div class="p-4">
                                    <p class="text-gray-700 dark:text-gray-300 mb-2"><strong>Nom:</strong>
                                        {{ $this->selectedPrescription->patient->nom ?? '-' }}
                                        {{ $this->selectedPrescription->patient->prenom ?? '' }}</p>
                                    <p class="text-gray-700 dark:text-gray-300 mb-2"><strong>Téléphone:</strong>
                                        {{ $this->selectedPrescription->patient->telephone ?? '-' }}</p>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Sexe:</strong>
                                        {{ $this->selectedPrescription->patient->sexe ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Informations Prescription --}}
                        <div class="col-span-1">
                            <div
                                class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-sm h-full">
                                <div
                                    class="px-4 py-2 bg-gray-50 dark:bg-gray-600 border-b border-gray-200 dark:border-gray-500 rounded-t-lg flex items-center">
                                    <i class="fas fa-info-circle mr-2 text-gray-700 dark:text-gray-300"></i>
                                    <span class="font-medium text-gray-900 dark:text-white">Prescription</span>
                                </div>
                                <div class="p-4">
                                    <p class="text-gray-700 dark:text-gray-300 mb-2"><strong>Prescripteur:</strong> Dr.
                                        {{ $this->selectedPrescription->prescripteur->nom ?? '-' }}</p>
                                    <p class="text-gray-700 dark:text-gray-300 mb-2"><strong>Technicien:</strong>
                                        {{ $this->selectedPrescription->technicien->name ?? 'Non assigné' }}</p>
                                    <p class="text-gray-700 dark:text-gray-300 mb-2"><strong>Statut:</strong>
                                        @php $s = $statusLabels[$this->selectedPrescription->status] ?? ['label' => '-', 'color' => 'secondary']; @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $s['color'] === 'warning' ? 'yellow' : ($s['color'] === 'info' ? 'blue' : ($s['color'] === 'primary' ? 'indigo' : ($s['color'] === 'success' ? 'green' : ($s['color'] === 'danger' ? 'red' : 'gray')))) }}-100 dark:bg-{{ $s['color'] === 'warning' ? 'yellow' : ($s['color'] === 'info' ? 'blue' : ($s['color'] === 'primary' ? 'indigo' : ($s['color'] === 'success' ? 'green' : ($s['color'] === 'danger' ? 'red' : 'gray')))) }}-900 text-{{ $s['color'] === 'warning' ? 'yellow' : ($s['color'] === 'info' ? 'blue' : ($s['color'] === 'primary' ? 'indigo' : ($s['color'] === 'success' ? 'green' : ($s['color'] === 'danger' ? 'red' : 'gray')))) }}-800 dark:text-{{ $s['color'] === 'warning' ? 'yellow' : ($s['color'] === 'info' ? 'blue' : ($s['color'] === 'primary' ? 'indigo' : ($s['color'] === 'success' ? 'green' : ($s['color'] === 'danger' ? 'red' : 'gray')))) }}-200">{{ $s['label'] }}</span>
                                    </p>
                                    <p class="text-gray-700 dark:text-gray-300"><strong>Date:</strong>
                                        {{ $this->selectedPrescription->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Analyses --}}
                        <div class="col-span-2">
                            <div
                                class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-sm">
                                <div
                                    class="px-4 py-2 bg-gray-50 dark:bg-gray-600 border-b border-gray-200 dark:border-gray-500 rounded-t-lg flex items-center">
                                    <i class="fas fa-vial mr-2 text-gray-700 dark:text-gray-300"></i>
                                    <span class="font-medium text-gray-900 dark:text-white">Analyses
                                        ({{ $this->selectedPrescription->analyses->count() }})</span>
                                </div>
                                <div class="p-0">
                                    <ul class="divide-y divide-gray-200 dark:divide-gray-600">
                                        @forelse($this->selectedPrescription->analyses as $analyse)
                                            <li
                                                class="px-4 py-3 flex items-center justify-between text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-150">
                                                {{ $analyse->designation }}
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-200">{{ $analyse->pivot->status ?? '-' }}</span>
                                            </li>
                                        @empty
                                            <li class="px-4 py-3 text-gray-500 dark:text-gray-400">Aucune analyse</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="px-6 py-4 bg-gray-50 dark:bg-gray-700 flex justify-end space-x-3 rounded-b-lg border-t border-gray-200 dark:border-gray-600">
                    <button type="button"
                        class="inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-150"
                        wire:click="closeDetailsModal">Fermer</button>
                    <button type="button"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-yellow-500 dark:bg-yellow-600 hover:bg-yellow-600 dark:hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 dark:focus:ring-yellow-400 transition-all duration-150"
                        wire:click="openChangeStatusModal({{ $this->selectedPrescription->id }})">
                        <i class="fas fa-exchange-alt mr-2"></i>Changer statut
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Modal Changement de Statut --}}
@if($showChangeStatusModal)
    <div
        class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 dark:bg-opacity-90 flex items-center justify-center transition-opacity duration-300">
        <div class="relative w-full max-w-lg mx-auto p-4">
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl max-h-[90vh] flex flex-col transform transition-all duration-300">
                <div
                    class="px-6 py-4 bg-yellow-500 dark:bg-yellow-600 text-white flex items-center justify-between rounded-t-lg">
                    <h5 class="text-xl font-semibold flex items-center">
                        <i class="fas fa-exchange-alt mr-2"></i>
                        Changer le statut
                    </h5>
                    <button type="button"
                        class="text-white hover:text-gray-200 focus:outline-none transition-colors duration-150"
                        wire:click="closeChangeStatusModal" aria-label="Close">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>
                <div class="p-6 flex-grow overflow-y-auto">
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-2">Nouveau
                            statut</label>
                        <select wire:model="newStatus"
                            class="form-select block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-300 dark:focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 focus:ring-opacity-50 text-sm transition-colors duration-200">
                            <option value="">-- Sélectionner --</option>
                            @foreach($statusLabels as $key => $config)
                                <option value="{{ $key }}">{{ $config['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-2">Commentaire
                            (optionnel)</label>
                        <textarea wire:model="commentaire"
                            class="form-textarea block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-300 dark:focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 focus:ring-opacity-50 text-sm transition-colors duration-200"
                            rows="3" placeholder="Raison du changement..."></textarea>
                    </div>
                </div>
                <div
                    class="px-6 py-4 bg-gray-50 dark:bg-gray-700 flex justify-end space-x-3 rounded-b-lg border-t border-gray-200 dark:border-gray-600">
                    <button type="button"
                        class="inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-150"
                        wire:click="closeChangeStatusModal">Annuler</button>
                    <button type="button"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-yellow-500 dark:bg-yellow-600 hover:bg-yellow-600 dark:hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 dark:focus:ring-yellow-400 transition-all duration-150"
                        wire:click="changeStatus">
                        <i class="fas fa-save mr-2"></i>Confirmer
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Modal Assignation Technicien --}}
@if($showAssignModal)
    <div
        class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 dark:bg-opacity-90 flex items-center justify-center transition-opacity duration-300">
        <div class="relative w-full max-w-lg mx-auto p-4">
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl max-h-[90vh] flex flex-col transform transition-all duration-300">
                <div
                    class="px-6 py-4 bg-green-600 dark:bg-green-700 text-white flex items-center justify-between rounded-t-lg">
                    <h5 class="text-xl font-semibold flex items-center">
                        <i class="fas fa-user-plus mr-2"></i>
                        Assigner un technicien
                    </h5>
                    <button type="button"
                        class="text-white hover:text-gray-200 focus:outline-none transition-colors duration-150"
                        wire:click="closeAssignModal" aria-label="Close">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>
                <div class="p-6 flex-grow overflow-y-auto">
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-2">Technicien</label>
                        <select wire:model="technicienId"
                            class="form-select block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-300 dark:focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 focus:ring-opacity-50 text-sm transition-colors duration-200">
                            <option value="">-- Sélectionner un technicien --</option>
                            @foreach($this->techniciens as $technicien)
                                <option value="{{ $technicien->id }}">{{ $technicien->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div
                    class="px-6 py-4 bg-gray-50 dark:bg-gray-700 flex justify-end space-x-3 rounded-b-lg border-t border-gray-200 dark:border-gray-600">
                    <button type="button"
                        class="inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-150"
                        wire:click="closeAssignModal">Annuler</button>
                    <button type="button"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 dark:bg-green-700 hover:bg-green-700 dark:hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-green-400 transition-all duration-150"
                        wire:click="assignTechnician">
                        <i class="fas fa-check mr-2"></i>Assigner
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Modal Confirmation Paiement --}}
@if($showConfirmPaymentModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl w-full max-w-md transform transition-all border border-slate-200 dark:border-slate-800">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <em class="ni ni-check-circle-fill text-3xl text-green-600 dark:text-green-400"></em>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Confirmer le paiement</h3>
                <p class="text-slate-500 dark:text-slate-400 mb-6">
                    Êtes-vous sûr de vouloir marquer cette prescription comme <span class="font-bold text-green-600">payée</span> ?
                </p>
                <div class="flex gap-3 justify-center">
                    <button wire:click="resetModal" 
                            class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        Annuler
                    </button>
                    <button wire:click="executeMarquerCommePayé" 
                            class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-green-600 hover:bg-green-700 shadow-lg shadow-green-600/20 transition-all">
                        Confirmer le paiement
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Modal Confirmation Annulation Paiement --}}
@if($showConfirmUnpaymentModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl w-full max-w-md transform transition-all border border-slate-200 dark:border-slate-800">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <em class="ni ni-alert-fill text-3xl text-red-600 dark:text-red-400"></em>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Annuler le paiement</h3>
                <p class="text-slate-500 dark:text-slate-400 mb-6">
                    Êtes-vous sûr de vouloir marquer cette prescription comme <span class="font-bold text-red-600">non payée</span> ?
                </p>
                <div class="flex gap-3 justify-center">
                    <button wire:click="resetModal" 
                            class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        Retour
                    </button>
                    <button wire:click="executeMarquerCommeNonPayé" 
                            class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-red-600 hover:bg-red-700 shadow-lg shadow-red-600/20 transition-all">
                        Confirmer l'annulation
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif