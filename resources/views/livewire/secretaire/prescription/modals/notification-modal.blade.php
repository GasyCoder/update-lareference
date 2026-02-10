<div>
    @if($show)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                    wire:click="close"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-middle bg-white dark:bg-slate-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-slate-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900 sm:mx-0 sm:h-10 sm:w-10">
                                <em class="ni ni-bell text-blue-600 dark:text-blue-200"></em>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-slate-800 dark:text-slate-100"
                                    id="modal-title">
                                    Notifier le patient
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <p class="text-sm text-slate-500 dark:text-slate-400">
                                        Patient : <strong>{{ $prescription->patient->nom }}
                                            {{ $prescription->patient->prenom }}</strong><br>
                                        Référence : <strong>{{ $prescription->reference }}</strong>
                                    </p>

                                    {{-- Choix du type --}}
                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Mode
                                            d'envoi</label>
                                        <div class="flex flex-col gap-2">
                                            @if($hasSms && $hasEmail)
                                                <label
                                                    class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 border-gray-200 dark:border-slate-600">
                                                    <input type="radio" wire:model.live="type" value="both"
                                                        class="text-primary-600">
                                                    <span class="text-sm text-slate-600 dark:text-slate-300">SMS et
                                                        E-mail</span>
                                                </label>
                                            @endif

                                            @if($hasSms)
                                                <label
                                                    class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 border-gray-200 dark:border-slate-600">
                                                    <input type="radio" wire:model.live="type" value="sms"
                                                        class="text-primary-600">
                                                    <span class="text-sm text-slate-600 dark:text-slate-300">SMS uniquement
                                                        ({{ $prescription->patient->telephone }})</span>
                                                </label>
                                            @endif

                                            @if($hasEmail)
                                                <label
                                                    class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 border-gray-200 dark:border-slate-600">
                                                    <input type="radio" wire:model.live="type" value="email"
                                                        class="text-primary-600">
                                                    <span class="text-sm text-slate-600 dark:text-slate-300">E-mail uniquement
                                                        ({{ $prescription->patient->email }})</span>
                                                </label>
                                            @endif

                                            @if(!$hasSms && !$hasEmail)
                                                <div class="p-3 bg-red-100 text-red-700 rounded-lg text-sm">
                                                    Aucune coordonnée disponible (téléphone ou email) pour ce patient.
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Message --}}
                                    @if($hasSms || $hasEmail)
                                        <div class="space-y-2">
                                            <label
                                                class="text-sm font-semibold text-slate-700 dark:text-slate-200">Message</label>
                                            <textarea wire:model="message" rows="5"
                                                class="w-full text-sm border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 rounded-lg focus:ring-primary-500 focus:border-primary-500"></textarea>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-slate-750 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        @if($hasSms || $hasEmail)
                            <button type="button" wire:click="send"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none sm:w-auto sm:text-sm">
                                Envoyer
                            </button>
                        @endif
                        <button type="button" wire:click="close"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-slate-600 shadow-sm px-4 py-2 bg-white dark:bg-slate-800 text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>