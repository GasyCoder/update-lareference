{{-- livewire.secretaire.prescription.partials.clinique --}}
@if($etape === 'clinique')
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200/70 dark:border-slate-700/80 overflow-hidden">

        {{-- HEADER --}}
        @if($isEditMode)
            <div class="px-5 py-4 border-b border-slate-200/60 dark:border-slate-700/70 bg-gradient-to-r from-orange-50 to-amber-50 dark:from-slate-800 dark:to-slate-800">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3 min-w-0">
                        <div class="w-11 h-11 bg-orange-600 rounded-xl flex items-center justify-center shadow-sm shrink-0">
                            <em class="ni ni-notes text-white text-base"></em>
                        </div>
                        <div class="min-w-0">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100 truncate">
                                Modification — Informations cliniques
                            </h2>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 truncate">
                                Modifier les renseignements médicaux et la prescription.
                            </p>
                        </div>
                    </div>

                    <div class="shrink-0 flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border border-orange-200 text-orange-800 bg-orange-50 dark:bg-orange-900/15 dark:text-orange-200 dark:border-orange-800/30">
                            <em class="ni ni-edit"></em> Édition
                        </span>
                        <span class="hidden sm:inline-flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                            <span class="inline-flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                            </span>
                            Étape 2/7
                        </span>
                    </div>
                </div>
            </div>
        @else
            <div class="px-5 py-4 border-b border-slate-200/60 dark:border-slate-700/70 bg-gradient-to-r from-cyan-50 to-blue-50 dark:from-slate-800 dark:to-slate-800">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3 min-w-0">
                        <div class="w-11 h-11 bg-cyan-600 rounded-xl flex items-center justify-center shadow-sm shrink-0">
                            <em class="ni ni-notes text-white text-base"></em>
                        </div>
                        <div class="min-w-0">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100 truncate">
                                Informations cliniques
                            </h2>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 truncate">
                                Renseignements médicaux et prescription.
                            </p>
                        </div>
                    </div>

                    <div class="shrink-0 flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border border-cyan-200 text-cyan-800 bg-cyan-50 dark:bg-cyan-900/15 dark:text-cyan-200 dark:border-cyan-800/30">
                            <em class="ni ni-check-circle"></em> Clinique
                        </span>
                        <span class="hidden sm:inline-flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                            <span class="inline-flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                            </span>
                            Étape 2/7
                        </span>
                    </div>
                </div>
            </div>
        @endif

        <div class="p-5 space-y-4">

            {{-- LIGNE 1 : Prescripteur + Type + Poids (poids remonte en haut) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                {{-- Prescripteur --}}
                <div class="rounded-xl border border-slate-200/70 dark:border-slate-700/70 bg-white dark:bg-slate-800 p-4">
                    <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">
                        <em class="ni ni-user-md mr-1.5 @if($isEditMode) text-orange-600 dark:text-orange-300 @else text-cyan-600 dark:text-cyan-300 @endif"></em>
                        Prescripteur <span class="text-red-500">*</span>
                    </label>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <em class="ni ni-user-md text-slate-400 dark:text-slate-500 text-base"></em>
                        </div>

                        @if($isEditMode)
                            <select wire:model="prescripteurId"
                                    class="w-full pl-10 pr-10 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-700
                                           border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-slate-100
                                           focus:outline-none focus:ring-4 focus:ring-orange-600/15 focus:border-orange-500
                                           hover:border-slate-300 dark:hover:border-slate-500 transition
                                           @error('prescripteurId') border-red-400 dark:border-red-600 focus:ring-red-500/20 focus:border-red-500 @enderror">
                                <option value="" class="text-slate-400">Sélectionner un prescripteur...</option>
                                @foreach($prescripteurs as $prescripteur)
                                    <option value="{{ $prescripteur->id }}" class="text-slate-900 dark:text-slate-100">
                                        {{ $prescripteur->nom }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <select wire:model="prescripteurId"
                                    class="w-full pl-10 pr-10 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-700
                                           border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-slate-100
                                           focus:outline-none focus:ring-4 focus:ring-cyan-600/15 focus:border-cyan-500
                                           hover:border-slate-300 dark:hover:border-slate-500 transition
                                           @error('prescripteurId') border-red-400 dark:border-red-600 focus:ring-red-500/20 focus:border-red-500 @enderror">
                                <option value="" class="text-slate-400">Sélectionner un prescripteur...</option>
                                @foreach($prescripteurs as $prescripteur)
                                    <option value="{{ $prescripteur->id }}" class="text-slate-900 dark:text-slate-100">
                                        {{ $prescripteur->nom }}
                                    </option>
                                @endforeach
                            </select>
                        @endif

                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                            <em class="ni ni-chevron-down text-slate-400 dark:text-slate-500 text-xs"></em>
                        </div>
                    </div>

                    @error('prescripteurId')
                        <p class="mt-2 text-xs text-red-600 dark:text-red-400 flex items-center gap-1.5">
                            <em class="ni ni-alert-circle"></em>{{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Type patient --}}
                <div class="rounded-xl border border-slate-200/70 dark:border-slate-700/70 bg-white dark:bg-slate-800 p-4">
                    <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">
                        <em class="ni ni-building mr-1.5 text-blue-600 dark:text-blue-300"></em>
                        Type de patient
                    </label>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <em class="ni ni-building text-slate-400 dark:text-slate-500 text-base"></em>
                        </div>

                        @if($isEditMode)
                            <select wire:model="patientType"
                                    class="w-full pl-10 pr-10 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-700
                                           border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-slate-100
                                           focus:outline-none focus:ring-4 focus:ring-orange-600/15 focus:border-orange-500
                                           hover:border-slate-300 dark:hover:border-slate-500 transition">
                                <option value="EXTERNE" class="text-slate-900 dark:text-slate-100">🏠 Externe</option>
                                <option value="HOSPITALISE" class="text-slate-900 dark:text-slate-100">🏥 Hospitalisé</option>
                                <option value="URGENCE-JOUR" class="text-slate-900 dark:text-slate-100">🚨 Urgence Jour</option>
                                <option value="URGENCE-NUIT" class="text-slate-900 dark:text-slate-100">🌙 Urgence Nuit</option>
                            </select>
                        @else
                            <select wire:model="patientType"
                                    class="w-full pl-10 pr-10 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-700
                                           border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-slate-100
                                           focus:outline-none focus:ring-4 focus:ring-cyan-600/15 focus:border-cyan-500
                                           hover:border-slate-300 dark:hover:border-slate-500 transition">
                                <option value="EXTERNE" class="text-slate-900 dark:text-slate-100">🏠 Externe</option>
                                <option value="HOSPITALISE" class="text-slate-900 dark:text-slate-100">🏥 Hospitalisé</option>
                                <option value="URGENCE-JOUR" class="text-slate-900 dark:text-slate-100">🚨 Urgence Jour</option>
                                <option value="URGENCE-NUIT" class="text-slate-900 dark:text-slate-100">🌙 Urgence Nuit</option>
                            </select>
                        @endif

                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                            <em class="ni ni-chevron-down text-slate-400 dark:text-slate-500 text-xs"></em>
                        </div>
                    </div>

                    <p class="mt-2 text-xs text-slate-600 dark:text-slate-400 flex items-center gap-1.5">
                        <em class="ni ni-info-circle"></em> Utile pour le tri et la prise en charge.
                    </p>
                </div>

                {{-- Poids (remonté ici) --}}
                <div class="rounded-xl border border-slate-200/70 dark:border-slate-700/70 bg-white dark:bg-slate-800 p-4">
                    <div class="flex items-center justify-between gap-3 mb-2.5">
                        <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200">
                            <em class="ni ni-activity mr-1.5 text-orange-600 dark:text-orange-300"></em>
                            Poids
                        </label>
                        <span class="text-xs text-slate-500 dark:text-slate-400">kg</span>
                    </div>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <em class="ni ni-activity text-slate-400 dark:text-slate-500 text-base"></em>
                        </div>

                        @if($isEditMode)
                            <input type="number"
                                   wire:model="poids"
                                   step="0.1" min="0"
                                   placeholder="Ex: 65.5"
                                   class="w-full pl-10 pr-12 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-700
                                          border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-slate-100
                                          placeholder-slate-400 dark:placeholder-slate-500
                                          focus:outline-none focus:ring-4 focus:ring-orange-600/15 focus:border-orange-500 transition">
                        @else
                            <input type="number"
                                   wire:model="poids"
                                   step="0.1" min="0"
                                   placeholder="Ex: 65.5"
                                   class="w-full pl-10 pr-12 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-700
                                          border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-slate-100
                                          placeholder-slate-400 dark:placeholder-slate-500
                                          focus:outline-none focus:ring-4 focus:ring-cyan-600/15 focus:border-cyan-500 transition">
                        @endif

                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">kg</span>
                        </div>
                    </div>

                    <p class="mt-2 text-xs text-slate-600 dark:text-slate-400 flex items-center gap-1.5">
                        <em class="ni ni-info-circle"></em> Optionnel — utile pour le calcul des doses.
                    </p>
                </div>
            </div>

            {{-- LIGNE 2 : Date naissance + Âge (âge réduit et collé à la naissance) --}}
            <div class="rounded-xl border border-slate-200/70 dark:border-slate-700/70 bg-white dark:bg-slate-800 p-4">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-900/15 border border-emerald-200/70 dark:border-emerald-800/30 flex items-center justify-center">
                            <em class="ni ni-calendar text-emerald-700 dark:text-emerald-300 text-sm"></em>
                        </span>
                        Naissance et âge
                    </h3>
                    <span class="text-xs text-slate-500 dark:text-slate-400">Recommandé</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
                    {{-- Date de naissance (2 colonnes) --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                            Date de naissance
                        </label>

                        @if($isEditMode)
                            <input type="date"
                                   wire:model.blur="dateNaissance"
                                   max="{{ date('Y-m-d') }}"
                                   min="1900-01-01"
                                   class="w-full px-4 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-700
                                          border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-slate-100
                                          focus:outline-none focus:ring-4 focus:ring-orange-600/15 focus:border-orange-500 transition
                                          @error('dateNaissance') border-red-400 dark:border-red-600 focus:ring-red-500/20 focus:border-red-500 @enderror">
                        @else
                            <input type="date"
                                   wire:model.blur="dateNaissance"
                                   max="{{ date('Y-m-d') }}"
                                   min="1900-01-01"
                                   class="w-full px-4 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-700
                                          border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-slate-100
                                          focus:outline-none focus:ring-4 focus:ring-cyan-600/15 focus:border-cyan-500 transition
                                          @error('dateNaissance') border-red-400 dark:border-red-600 focus:ring-red-500/20 focus:border-red-500 @enderror">
                        @endif

                        @error('dateNaissance')
                            <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1.5">
                                <em class="ni ni-alert-circle"></em>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Âge (compact, collé à droite) --}}
                    <div class="md:col-span-1">
                        @if($dateNaissance)
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                Âge
                            </label>

                            <div class="rounded-xl border border-blue-200/70 dark:border-blue-800/30 bg-blue-50 dark:bg-blue-900/15 px-4 py-2.5">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-sm font-bold text-blue-800 dark:text-blue-200">
                                        {{ $age }} {{ $uniteAge }}
                                    </span>
                                    <em class="ni ni-check-circle text-emerald-600 dark:text-emerald-300"></em>
                                </div>
                            </div>

                            <p class="mt-1.5 text-xs text-blue-700 dark:text-blue-300 flex items-center gap-1.5">
                                <em class="ni ni-info-circle"></em> Calcul auto.
                            </p>
                        @else
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                Âge <span class="text-red-500">*</span>
                            </label>

                            <div class="flex gap-2">
                                <div class="flex-1 relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <em class="ni ni-hash text-slate-400 dark:text-slate-500 text-base"></em>
                                    </div>

                                    @if($isEditMode)
                                        <input type="number"
                                               wire:model="age"
                                               min="0" max="150"
                                               placeholder="Ex: 32"
                                               class="w-full pl-10 pr-3 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-700
                                                      border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-slate-100
                                                      focus:outline-none focus:ring-4 focus:ring-orange-600/15 focus:border-orange-500 transition
                                                      @error('age') border-red-400 dark:border-red-600 focus:ring-red-500/20 focus:border-red-500 @enderror">
                                    @else
                                        <input type="number"
                                               wire:model="age"
                                               min="0" max="150"
                                               placeholder="Ex: 32"
                                               class="w-full pl-10 pr-3 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-700
                                                      border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-slate-100
                                                      focus:outline-none focus:ring-4 focus:ring-cyan-600/15 focus:border-cyan-500 transition
                                                      @error('age') border-red-400 dark:border-red-600 focus:ring-red-500/20 focus:border-red-500 @enderror">
                                    @endif
                                </div>

                                <div class="w-24">
                                    @if($isEditMode)
                                        <select wire:model="uniteAge"
                                                class="w-full px-3 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-700
                                                       border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-slate-100
                                                       focus:outline-none focus:ring-4 focus:ring-orange-600/15 focus:border-orange-500 transition">
                                            <option value="Ans">Ans</option>
                                            <option value="Mois">Mois</option>
                                            <option value="Jours">Jours</option>
                                        </select>
                                    @else
                                        <select wire:model="uniteAge"
                                                class="w-full px-3 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-700
                                                       border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-slate-100
                                                       focus:outline-none focus:ring-4 focus:ring-cyan-600/15 focus:border-cyan-500 transition">
                                            <option value="Ans">Ans</option>
                                            <option value="Mois">Mois</option>
                                            <option value="Jours">Jours</option>
                                        </select>
                                    @endif
                                </div>
                            </div>

                            @error('age')
                                <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1.5">
                                    <em class="ni ni-alert-circle"></em>{{ $message }}
                                </p>
                            @enderror

                            <p class="mt-1.5 text-xs text-amber-700 dark:text-amber-300 flex items-center gap-1.5">
                                <em class="ni ni-info-circle"></em> Ajoutez une date pour calcul auto.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- RENSEIGNEMENTS CLINIQUES --}}
            <div class="rounded-xl border border-slate-200/70 dark:border-slate-700/70 bg-white dark:bg-slate-800 p-4">
                <div class="flex items-center justify-between gap-3 mb-2.5">
                    <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200">
                        <em class="ni ni-clipboard mr-1.5 text-purple-600 dark:text-purple-300"></em>
                        Renseignements cliniques
                    </label>
                    <span class="text-xs text-slate-500 dark:text-slate-400">
                        {{ strlen($renseignementClinique ?? '') }} caractères
                    </span>
                </div>

                <div class="relative">
                    @if($isEditMode)
                        <textarea wire:model="renseignementClinique"
                                  rows="4"
                                  placeholder="Décrivez les symptômes, antécédents médicaux, indications spéciales, allergies connues..."
                                  class="w-full px-4 py-3 text-sm rounded-xl bg-white dark:bg-slate-700
                                         border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-slate-100
                                         placeholder-slate-400 dark:placeholder-slate-500 resize-none
                                         focus:outline-none focus:ring-4 focus:ring-orange-600/15 focus:border-orange-500 transition"></textarea>
                    @else
                        <textarea wire:model="renseignementClinique"
                                  rows="4"
                                  placeholder="Décrivez les symptômes, antécédents médicaux, indications spéciales, allergies connues..."
                                  class="w-full px-4 py-3 text-sm rounded-xl bg-white dark:bg-slate-700
                                         border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-slate-100
                                         placeholder-slate-400 dark:placeholder-slate-500 resize-none
                                         focus:outline-none focus:ring-4 focus:ring-cyan-600/15 focus:border-cyan-500 transition"></textarea>
                    @endif

                    <div class="absolute top-3 right-3 text-slate-400 dark:text-slate-500 pointer-events-none">
                        <em class="ni ni-edit text-sm"></em>
                    </div>
                </div>

                <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-600 dark:text-slate-400">
                    <span class="inline-flex items-center gap-1.5">
                        <em class="ni ni-shield-check text-emerald-600 dark:text-emerald-300"></em> Informations confidentielles
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <em class="ni ni-lock text-blue-600 dark:text-blue-300"></em> Données sécurisées
                    </span>
                </div>
            </div>

            {{-- CONSEILS --}}
            <div class="rounded-xl border border-indigo-200/60 dark:border-indigo-800/40 bg-indigo-50/70 dark:bg-indigo-900/10 p-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shrink-0 shadow-sm">
                        <em class="ni ni-bulb text-white text-sm"></em>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-sm font-semibold text-indigo-900 dark:text-indigo-200">
                            {{ $isEditMode ? 'Conseils pour une modification optimale' : 'Conseils pour une prescription optimale' }}
                        </h4>
                        <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-indigo-800 dark:text-indigo-300">
                            <span class="inline-flex items-center gap-1.5">
                                <em class="ni ni-check-circle text-emerald-600 dark:text-emerald-300"></em>
                                Naissance → âge calculé
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <em class="ni ni-check-circle text-emerald-600 dark:text-emerald-300"></em>
                                {{ $isEditMode ? 'Vérifiez les nouvelles allergies' : 'Vérifiez les allergies connues' }}
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <em class="ni ni-check-circle text-emerald-600 dark:text-emerald-300"></em>
                                {{ $isEditMode ? 'Mettez à jour les traitements' : 'Notez les traitements en cours' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- NAVIGATION --}}
            <div class="pt-4 border-t border-slate-200/60 dark:border-slate-700/70 flex flex-col sm:flex-row items-center justify-between gap-3">
                <button wire:click="allerEtape('patient')"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                               bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600
                               text-slate-700 dark:text-slate-200 text-sm font-semibold transition
                               focus:outline-none focus:ring-4 focus:ring-slate-900/5 dark:focus:ring-white/10">
                    <em class="ni ni-arrow-left"></em> Retour patient
                </button>

                <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                    <span class="inline-flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        @if($isEditMode)
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                        @else
                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span>
                        @endif
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    </span>
                    Étape 2/7
                </div>

                @if($isEditMode)
                    <button wire:click="validerInformationsCliniques"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                                   bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-sm hover:shadow transition
                                   focus:outline-none focus:ring-4 focus:ring-emerald-600/20
                                   disabled:opacity-50 disabled:cursor-not-allowed">
                        Modifier les analyses <em class="ni ni-arrow-right"></em>
                    </button>
                @else
                    <button wire:click="validerInformationsCliniques"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl
                                   bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold shadow-sm hover:shadow transition
                                   focus:outline-none focus:ring-4 focus:ring-primary-600/20
                                   disabled:opacity-50 disabled:cursor-not-allowed">
                        Continuer vers analyses <em class="ni ni-arrow-right"></em>
                    </button>
                @endif
            </div>

        </div>
    </div>
@endif
