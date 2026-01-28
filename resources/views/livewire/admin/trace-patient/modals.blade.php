{{-- resources/views/livewire/admin/trace-patient/modals.blade.php --}}

<!-- Modal Suppression Définitive Patient -->
@if($confirmingForceDeletePatient)
<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('confirmingForceDeletePatient', false)"></div>

    <!-- Modal Container -->
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/20 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white" id="modal-title">
                            Supprimer définitivement ce patient ?
                        </h3>
                        <div class="mt-2">
                            @if($patientToDelete)
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                                <strong class="text-gray-900 dark:text-white">{{ $patientToDelete->nom }} {{ $patientToDelete->prenom }}</strong><br>
                                Dossier : {{ $patientToDelete->numero_dossier }}
                            </p>
                            
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3 mb-3">
                                <p class="text-sm text-red-800 dark:text-red-200 font-semibold mb-2">
                                    ⚠️ Attention : Cette action est IRRÉVERSIBLE !
                                </p>
                                <ul class="text-sm text-red-700 dark:text-red-300 space-y-1 ml-4 list-disc">
                                    <li>Le patient sera définitivement supprimé</li>
                                    @if($patientToDelete->prescriptions_count > 0)
                                    <li><strong>{{ $patientToDelete->prescriptions_count }} prescription(s)</strong> seront supprimées</li>
                                    <li>Tous les paiements associés seront supprimés</li>
                                    <li>Tous les résultats d'analyses seront supprimés</li>
                                    <li>Tous les tubes et prélèvements seront supprimés</li>
                                    @else
                                    <li>Aucune prescription associée</li>
                                    @endif
                                </ul>
                            </div>
                            
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Toutes les données seront <span class="font-bold text-red-600 dark:text-red-400">perdues définitivement</span>.
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                <button type="button" 
                    wire:click="forceDeletePatient"
                    class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:w-auto">
                    Supprimer définitivement
                </button>
                <button type="button" 
                    wire:click="$set('confirmingForceDeletePatient', false)"
                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-600 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-500 hover:bg-gray-50 dark:hover:bg-gray-500 sm:mt-0 sm:w-auto">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modal Suppression Définitive Prescription -->
@if($confirmingForceDeletePrescription)
<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('confirmingForceDeletePrescription', false)"></div>

    <!-- Modal Container -->
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/20 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white" id="modal-title">
                            Supprimer définitivement cette prescription ?
                        </h3>
                        <div class="mt-2">
                            @if($prescriptionToDelete)
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                                <strong class="text-gray-900 dark:text-white">{{ $prescriptionToDelete->reference }}</strong><br>
                                Patient : {{ $prescriptionToDelete->patient->nom ?? 'N/A' }} {{ $prescriptionToDelete->patient->prenom ?? '' }}
                            </p>
                            
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3 mb-3">
                                <p class="text-sm text-red-800 dark:text-red-200 font-semibold mb-2">
                                    ⚠️ Attention : Cette action est IRRÉVERSIBLE !
                                </p>
                                <ul class="text-sm text-red-700 dark:text-red-300 space-y-1 ml-4 list-disc">
                                    <li>La prescription sera définitivement supprimée</li>
                                    <li>Tous les paiements associés seront supprimés</li>
                                    <li>Tous les résultats d'analyses seront supprimés</li>
                                    <li>Tous les tubes et prélèvements seront supprimés</li>
                                    <li>Tous les antibiogrammes seront supprimés</li>
                                    <li><strong>Le patient ne sera PAS supprimé</strong></li>
                                </ul>
                            </div>
                            
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Toutes les données de cette prescription seront <span class="font-bold text-red-600 dark:text-red-400">perdues définitivement</span>.
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                <button type="button" 
                    wire:click="forceDeletePrescription"
                    class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:w-auto">
                    Supprimer définitivement
                </button>
                <button type="button" 
                    wire:click="$set('confirmingForceDeletePrescription', false)"
                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-600 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-500 hover:bg-gray-50 dark:hover:bg-gray-500 sm:mt-0 sm:w-auto">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modal Vider Corbeille Patients -->
@if($confirmingEmptyPatientsTrash)
<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('confirmingEmptyPatientsTrash', false)"></div>

    <!-- Modal Container -->
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/20 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white" id="modal-title">
                            Vider la corbeille des patients ?
                        </h3>
                        <div class="mt-2">
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3 mb-3">
                                <p class="text-sm text-red-800 dark:text-red-200 font-semibold mb-2">
                                    ⚠️ DANGER : Cette action est IRRÉVERSIBLE !
                                </p>
                                <ul class="text-sm text-red-700 dark:text-red-300 space-y-1 ml-4 list-disc">
                                    <li><strong>{{ $patientsCount }} patient(s)</strong> seront supprimés</li>
                                    <li>Toutes leurs prescriptions seront supprimées</li>
                                    <li>Tous les paiements seront supprimés</li>
                                    <li>Tous les résultats seront supprimés</li>
                                    <li>Toutes les données seront perdues</li>
                                </ul>
                            </div>
                            
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Cette action est <span class="font-bold text-red-600 dark:text-red-400">IRRÉVERSIBLE</span>. Êtes-vous absolument sûr ?
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                <button type="button" 
                    wire:click="emptyPatientsTrash"
                    class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:w-auto">
                    Oui, tout supprimer
                </button>
                <button type="button" 
                    wire:click="$set('confirmingEmptyPatientsTrash', false)"
                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-600 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-500 hover:bg-gray-50 dark:hover:bg-gray-500 sm:mt-0 sm:w-auto">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modal Vider Corbeille Prescriptions -->
@if($confirmingEmptyPrescriptionsTrash)
<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('confirmingEmptyPrescriptionsTrash', false)"></div>

    <!-- Modal Container -->
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/20 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white" id="modal-title">
                            Vider la corbeille des prescriptions ?
                        </h3>
                        <div class="mt-2">
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3 mb-3">
                                <p class="text-sm text-red-800 dark:text-red-200 font-semibold mb-2">
                                    ⚠️ DANGER : Cette action est IRRÉVERSIBLE !
                                </p>
                                <ul class="text-sm text-red-700 dark:text-red-300 space-y-1 ml-4 list-disc">
                                    <li><strong>{{ $prescriptionsCount }} prescription(s)</strong> seront supprimées</li>
                                    <li>Tous les paiements associés seront supprimés</li>
                                    <li>Tous les résultats seront supprimés</li>
                                    <li>Tous les tubes seront supprimés</li>
                                    <li>Tous les antibiogrammes seront supprimés</li>
                                    <li><strong>Les patients ne seront PAS supprimés</strong></li>
                                </ul>
                            </div>
                            
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Cette action est <span class="font-bold text-red-600 dark:text-red-400">IRRÉVERSIBLE</span>. Êtes-vous absolument sûr ?
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                <button type="button" 
                    wire:click="emptyPrescriptionsTrash"
                    class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:w-auto">
                    Oui, tout supprimer
                </button>
                <button type="button" 
                    wire:click="$set('confirmingEmptyPrescriptionsTrash', false)"
                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-600 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-500 hover:bg-gray-50 dark:hover:bg-gray-500 sm:mt-0 sm:w-auto">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>
@endif