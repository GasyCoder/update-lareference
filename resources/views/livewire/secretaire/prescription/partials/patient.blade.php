{{-- livewire.secretaire.prescription.partials.patient --}}
@if($etape === 'patient')
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200/70 dark:border-slate-700/80 overflow-hidden">

    {{-- HEADER --}}
    <div class="px-5 py-4 border-b border-slate-200/60 dark:border-slate-700/70">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-3 min-w-0">
                @if($isEditMode)
                    <div class="w-11 h-11 bg-orange-600 rounded-xl flex items-center justify-center shadow-sm shrink-0">
                        <em class="ni ni-user text-white text-base"></em>
                    </div>
                @else
                    <div class="w-11 h-11 bg-primary-600 rounded-xl flex items-center justify-center shadow-sm shrink-0">
                        <em class="ni ni-user text-white text-base"></em>
                    </div>
                @endif

                <div class="min-w-0">
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100 truncate">
                        Patient
                        <span class="font-normal text-slate-600 dark:text-slate-400">
                            — {{ $isEditMode ? 'Modification' : 'Sélection / création' }}
                        </span>
                    </h2>
                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 truncate">
                        {{ $isEditMode ? 'Choisissez un autre patient ou modifiez le dossier.' : 'Sélectionnez un patient existant ou créez un nouveau dossier.' }}
                    </p>
                </div>
            </div>

            @if(isset($patientsResultats) && $patientsResultats->count() > 0)
                <span class="shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 dark:bg-emerald-900/15 text-emerald-700 dark:text-emerald-300 border border-emerald-200/70 dark:border-emerald-800/40">
                    <em class="ni ni-check-circle"></em> {{ $patientsResultats->count() }}
                </span>
            @endif
        </div>

        {{-- BOUTONS OUTLINE : PATIENT EXISTANT / NOUVEAU (couleurs différentes) --}}
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">

            {{-- Patient existant (PRIMARY) --}}
            @if(!$nouveauPatient)
                <button type="button"
                        @if($isEditMode)
                            wire:click="$set('nouveauPatient', false); $set('patient', null); $set('recherchePatient','')"
                        @else
                            wire:click="$set('nouveauPatient', false)"
                        @endif
                        class="w-full rounded-xl border-2 px-4 py-3 text-left transition bg-transparent focus:outline-none focus:ring-4 shadow-sm
                               border-primary-600 bg-primary-50 dark:bg-primary-900/15 text-primary-700 dark:text-primary-300 focus:ring-primary-600/15">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl flex items-center justify-center border border-primary-200 dark:border-primary-800/40 bg-white/60 dark:bg-primary-900/10">
                                <em class="ni ni-search text-base text-primary-600 dark:text-primary-300"></em>
                            </span>
                            <div>
                                <div class="text-sm font-semibold">Patient existant</div>
                                <div class="text-xs text-slate-600 dark:text-slate-400">Rechercher et sélectionner</div>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold">
                            <em class="ni ni-check"></em> Actif
                        </span>
                    </div>
                </button>
            @else
                <button type="button"
                        @if($isEditMode)
                            wire:click="$set('nouveauPatient', false); $set('patient', null); $set('recherchePatient','')"
                        @else
                            wire:click="$set('nouveauPatient', false)"
                        @endif
                        class="w-full rounded-xl border-2 px-4 py-3 text-left transition bg-transparent focus:outline-none focus:ring-4
                               border-slate-200 dark:border-slate-600 text-slate-800 dark:text-slate-100 hover:border-slate-300 dark:hover:border-slate-500
                               focus:ring-primary-600/15">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl flex items-center justify-center border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/40">
                                <em class="ni ni-search text-base text-primary-600 dark:text-primary-300"></em>
                            </span>
                            <div>
                                <div class="text-sm font-semibold">Patient existant</div>
                                <div class="text-xs text-slate-600 dark:text-slate-400">Rechercher et sélectionner</div>
                            </div>
                        </div>
                        <em class="ni ni-arrow-right text-base text-slate-400 dark:text-slate-500"></em>
                    </div>
                </button>
            @endif

            {{-- Nouveau patient (EMERALD) --}}
            @if($nouveauPatient)
                <button type="button"
                        @if($isEditMode)
                            wire:click="$set('patient', null); creerNouveauPatient"
                        @else
                            wire:click="creerNouveauPatient"
                        @endif
                        class="w-full rounded-xl border-2 px-4 py-3 text-left transition bg-transparent focus:outline-none focus:ring-4 shadow-sm
                               border-emerald-600 bg-emerald-50 dark:bg-emerald-900/15 text-emerald-700 dark:text-emerald-300 focus:ring-emerald-600/15">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl flex items-center justify-center border border-emerald-200 dark:border-emerald-800/40 bg-white/60 dark:bg-emerald-900/10">
                                <em class="ni ni-user-add text-base text-emerald-600 dark:text-emerald-300"></em>
                            </span>
                            <div>
                                <div class="text-sm font-semibold">Nouveau patient</div>
                                <div class="text-xs text-slate-600 dark:text-slate-400">Créer un dossier</div>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold">
                            <em class="ni ni-check"></em> Actif
                        </span>
                    </div>
                </button>
            @else
                <button type="button"
                        @if($isEditMode)
                            wire:click="$set('patient', null); creerNouveauPatient"
                        @else
                            wire:click="creerNouveauPatient"
                        @endif
                        class="w-full rounded-xl border-2 px-4 py-3 text-left transition bg-transparent focus:outline-none focus:ring-4
                               border-slate-200 dark:border-slate-600 text-slate-800 dark:text-slate-100 hover:border-slate-300 dark:hover:border-slate-500
                               focus:ring-emerald-600/15">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl flex items-center justify-center border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/40">
                                <em class="ni ni-user-add text-base text-emerald-600 dark:text-emerald-300"></em>
                            </span>
                            <div>
                                <div class="text-sm font-semibold">Nouveau patient</div>
                                <div class="text-xs text-slate-600 dark:text-slate-400">Créer un dossier</div>
                            </div>
                        </div>
                        <em class="ni ni-arrow-right text-base text-slate-400 dark:text-slate-500"></em>
                    </div>
                </button>
            @endif

        </div>

        <div class="mt-3 text-xs text-slate-600 dark:text-slate-400">
            Mode actuel :
            @if($nouveauPatient)
                <span class="font-semibold text-emerald-700 dark:text-emerald-300">Nouveau patient</span>
            @else
                <span class="font-semibold text-primary-700 dark:text-primary-300">Patient existant</span>
            @endif
        </div>
    </div>

    <div class="p-5 space-y-4">
        {{-- MODE : SÉLECTION --}}
        @if(!$nouveauPatient && (!$isEditMode || !$patient))

            {{-- RECHERCHE --}}
            <div class="rounded-xl border border-slate-200/70 dark:border-slate-700/70 bg-white dark:bg-slate-800 p-4">
                <label class="block text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">
                    <em class="ni ni-search mr-1.5 text-primary-600 dark:text-primary-300"></em>
                    Rechercher un patient
                </label>

                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <em class="ni ni-search text-slate-400 dark:text-slate-500 text-base"></em>
                    </div>

                    @if($isEditMode)
                        <input type="text"
                               wire:model.live.debounce.300ms="recherchePatient"
                               placeholder="Nom, prénom, référence ou téléphone…"
                               class="w-full pl-10 pr-10 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-700
                                      border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-slate-100
                                      placeholder-slate-400 dark:placeholder-slate-500
                                      focus:outline-none focus:ring-4 focus:ring-orange-600/15 focus:border-orange-500 transition">
                    @else
                        <input type="text"
                               wire:model.live.debounce.300ms="recherchePatient"
                               placeholder="Nom, prénom, référence ou téléphone…"
                               class="w-full pl-10 pr-10 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-700
                                      border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-slate-100
                                      placeholder-slate-400 dark:placeholder-slate-500
                                      focus:outline-none focus:ring-4 focus:ring-primary-600/15 focus:border-primary-500 transition">
                    @endif

                    @if($recherchePatient)
                        <button type="button"
                                wire:click="$set('recherchePatient', '')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-red-500 dark:hover:text-red-400 transition">
                            <em class="ni ni-cross-circle text-lg"></em>
                        </button>
                    @endif
                </div>

                @if(strlen($recherchePatient) > 0 && strlen($recherchePatient) < 2)
                    <p class="mt-2 text-xs text-amber-700 dark:text-amber-300 flex items-center gap-1.5">
                        <em class="ni ni-info-circle"></em> Tapez au moins 2 caractères.
                    </p>
                @endif
            </div>

            {{-- RÉSULTATS --}}
            @if(isset($patientsResultats) && $patientsResultats->count() > 0)
                <div class="rounded-xl border border-slate-200/70 dark:border-slate-700/70 bg-white dark:bg-slate-800 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Résultats</h3>
                        <span class="text-xs text-slate-600 dark:text-slate-400">Cliquez pour sélectionner</span>
                    </div>

                    <div class="space-y-2.5">
                        @foreach($patientsResultats as $patient_item)
                            <button type="button"
                                    wire:click="selectionnerPatient({{ $patient_item->id }})"
                                    class="group w-full text-left p-4 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800
                                           hover:bg-slate-50 dark:hover:bg-slate-700/30 hover:shadow-sm transition focus:outline-none focus:ring-4
                                           @if($isEditMode) focus:ring-orange-600/15 @else focus:ring-primary-600/15 @endif">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3 flex-1 min-w-0">
                                        @if($isEditMode)
                                            <div class="w-12 h-12 shrink-0 rounded-xl bg-orange-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                                {{ strtoupper(substr($patient_item->nom, 0, 1)) }}{{ strtoupper(substr($patient_item->prenom ?? '', 0, 1)) }}
                                            </div>
                                        @else
                                            <div class="w-12 h-12 shrink-0 rounded-xl bg-primary-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                                {{ strtoupper(substr($patient_item->nom, 0, 1)) }}{{ strtoupper(substr($patient_item->prenom ?? '', 0, 1)) }}
                                            </div>
                                        @endif

                                        <div class="min-w-0 flex-1">
                                            <div class="font-semibold text-slate-900 dark:text-slate-100 text-sm truncate">
                                                {{ $patient_item->nom }} {{ $patient_item->prenom }}
                                            </div>

                                            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1.5 text-xs text-slate-600 dark:text-slate-400">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md font-mono bg-slate-100 dark:bg-slate-700">
                                                    <em class="ni ni-id-badge mr-1 text-xs @if($isEditMode) text-orange-500 @else text-primary-500 @endif"></em>
                                                    {{ $patient_item->numero_dossier ?? $patient_item->reference }}
                                                </span>

                                                @if($patient_item->telephone)
                                                    <span class="inline-flex items-center">
                                                        <em class="ni ni-call mr-1 text-emerald-600 dark:text-emerald-300 text-xs"></em>
                                                        {{ $patient_item->telephone }}
                                                    </span>
                                                @endif

                                                @if($patient_item->date_naissance)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-900/15 text-blue-700 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/30">
                                                        <em class="ni ni-calendar mr-1 text-xs"></em>
                                                        {{ $patient_item->age_en_annees }} ans
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="shrink-0 flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition
                                                @if($isEditMode) text-orange-600 dark:text-orange-300 @else text-primary-600 dark:text-primary-300 @endif">
                                        <span class="text-xs font-semibold">{{ $isEditMode ? 'Changer' : 'Sélectionner' }}</span>
                                        <em class="ni ni-arrow-right text-base group-hover:translate-x-0.5 transition-transform"></em>
                                    </div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            @elseif(strlen($recherchePatient) >= 2)
                <div class="bg-amber-50 dark:bg-amber-900/10 rounded-xl border border-amber-200/70 dark:border-amber-800/40 p-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/20 flex items-center justify-center shrink-0">
                            <em class="ni ni-alert-circle text-amber-800 dark:text-amber-300 text-lg"></em>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-amber-900 dark:text-amber-200">Aucun patient trouvé</h4>
                            <p class="text-xs text-amber-800 dark:text-amber-300 mt-0.5">
                                Aucun résultat pour “{{ $recherchePatient }}”.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

        {{-- MODE : FORMULAIRE --}}
        @else
            <div class="rounded-xl border border-slate-200/70 dark:border-slate-700/70 bg-white dark:bg-slate-800 p-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                        {{ $isEditMode ? 'Mettre à jour le dossier patient' : 'Créer un dossier patient' }}
                    </h3>
                    <span class="text-xs text-slate-600 dark:text-slate-400">* obligatoires</span>
                </div>

                {{-- IDENTITÉ --}}
                <div class="rounded-xl border border-slate-200/70 dark:border-slate-700/70 p-4">
                    <h4 class="text-sm font-semibold text-slate-900 dark:text-slate-100 mb-3">Identité</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                Nom <span class="text-red-500">*</span>
                            </label>

                            @if($isEditMode)
                                <input type="text"
                                       wire:model.blur="nom"
                                       placeholder="Ex: RAJAONA"
                                       class="w-full px-4 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-700
                                              border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-slate-100
                                              placeholder-slate-400 dark:placeholder-slate-500
                                              focus:outline-none focus:ring-4 focus:ring-orange-600/15 focus:border-orange-500 transition
                                              @error('nom') border-red-400 dark:border-red-600 focus:ring-red-500/20 focus:border-red-500 @enderror">
                            @else
                                <input type="text"
                                       wire:model.blur="nom"
                                       placeholder="Ex: RAJAONA"
                                       class="w-full px-4 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-700
                                              border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-slate-100
                                              placeholder-slate-400 dark:placeholder-slate-500
                                              focus:outline-none focus:ring-4 focus:ring-primary-600/15 focus:border-primary-500 transition
                                              @error('nom') border-red-400 dark:border-red-600 focus:ring-red-500/20 focus:border-red-500 @enderror">
                            @endif

                            @error('nom')
                                <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1.5">
                                    <em class="ni ni-alert-circle"></em>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Prénom(s)</label>

                            @if($isEditMode)
                                <input type="text"
                                       wire:model.blur="prenom"
                                       placeholder="Ex: Miangaly"
                                       class="w-full px-4 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-700
                                              border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-slate-100
                                              placeholder-slate-400 dark:placeholder-slate-500
                                              focus:outline-none focus:ring-4 focus:ring-orange-600/15 focus:border-orange-500 transition">
                            @else
                                <input type="text"
                                       wire:model.blur="prenom"
                                       placeholder="Ex: Miangaly"
                                       class="w-full px-4 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-700
                                              border border-slate-200 dark:border-slate-600 text-slate-900 dark:text-slate-100
                                              placeholder-slate-400 dark:placeholder-slate-500
                                              focus:outline-none focus:ring-4 focus:ring-primary-600/15 focus:border-primary-500 transition">
                            @endif
                        </div>
                    </div>
                </div>

                {{-- CIVILITÉ (compacte) --}}
                <div class="mt-4 rounded-xl border border-slate-200/70 dark:border-slate-700/70 p-4">
                    <div class="flex items-center justify-between gap-3 mb-2.5">
                        <h4 class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                            Civilité <span class="text-red-500">*</span>
                        </h4>

                        @if(!empty($civilite))
                            <span class="text-xs px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/40 text-slate-700 dark:text-slate-200">
                                Sélectionné :
                                <span class="font-semibold">
                                    {{ $this->civilitesDisponibles[$civilite]['label'] ?? $civilite }}
                                </span>
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5">
                        @foreach($this->civilitesDisponibles as $value => $config)
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="civilite" value="{{ $value }}" class="sr-only peer">

                                <div class="relative rounded-xl border-2 px-3 py-2.5 bg-white dark:bg-slate-800 transition
                                            border-slate-200 dark:border-slate-600 hover:border-slate-300 dark:hover:border-slate-500
                                            hover:shadow-sm peer-focus-visible:ring-4 peer-focus-visible:ring-slate-900/5 dark:peer-focus-visible:ring-white/10
                                            @if($isEditMode)
                                                peer-checked:border-orange-600 peer-checked:bg-orange-50 dark:peer-checked:bg-orange-900/15
                                            @else
                                                peer-checked:border-primary-600 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-900/15
                                            @endif">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <div class="text-sm font-semibold text-slate-900 dark:text-slate-100 truncate">
                                                    {{ $config['label'] }}
                                                </div>

                                                @if($isEditMode)
                                                    <span class="hidden peer-checked:inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full border border-orange-200 text-orange-700 bg-orange-50 dark:bg-orange-900/15 dark:text-orange-200 dark:border-orange-800/30">
                                                        <em class="ni ni-check text-[11px]"></em> Sélectionné
                                                    </span>
                                                @else
                                                    <span class="hidden peer-checked:inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full border border-primary-200 text-primary-700 bg-primary-50 dark:bg-primary-900/15 dark:text-primary-200 dark:border-primary-800/30">
                                                        <em class="ni ni-check text-[11px]"></em> Sélectionné
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="mt-0.5 text-[11px] text-slate-600 dark:text-slate-400">
                                                @if($value === 'Monsieur') Homme
                                                @elseif($value === 'Madame') Femme
                                                @elseif($value === 'Enfant-garçon') Enfant
                                                @elseif($value === 'Enfant-fille') Enfant
                                                @else Autre
                                                @endif
                                            </div>
                                        </div>

                                        <span class="shrink-0 w-6 h-6 rounded-full flex items-center justify-center bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600
                                                     opacity-0 scale-95 transition peer-checked:opacity-100 peer-checked:scale-100">
                                            @if($isEditMode)
                                                <em class="ni ni-check text-xs text-orange-600"></em>
                                            @else
                                                <em class="ni ni-check text-xs text-primary-600"></em>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    @error('civilite')
                        <p class="mt-2 text-xs text-red-600 dark:text-red-400 flex items-center gap-1.5">
                            <em class="ni ni-alert-circle"></em>{{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- CONTACT --}}
                <div class="mt-4 rounded-xl border border-slate-200/70 dark:border-slate-700/70 p-4">
                    <h4 class="text-sm font-semibold text-slate-900 dark:text-slate-100 mb-3">Contact</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Téléphone</label>

                            @if($isEditMode)
                                <input type="tel" wire:model.blur="telephone" placeholder="+261 34 12 345 67"
                                       class="w-full px-4 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600
                                              text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500
                                              focus:outline-none focus:ring-4 focus:ring-orange-600/15 focus:border-orange-500 transition">
                            @else
                                <input type="tel" wire:model.blur="telephone" placeholder="+261 34 12 345 67"
                                       class="w-full px-4 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600
                                              text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500
                                              focus:outline-none focus:ring-4 focus:ring-primary-600/15 focus:border-primary-500 transition">
                            @endif
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Email</label>

                            @if($isEditMode)
                                <input type="email" wire:model.blur="email" placeholder="email@exemple.com"
                                       class="w-full px-4 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600
                                              text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500
                                              focus:outline-none focus:ring-4 focus:ring-orange-600/15 focus:border-orange-500 transition">
                            @else
                                <input type="email" wire:model.blur="email" placeholder="email@exemple.com"
                                       class="w-full px-4 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600
                                              text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500
                                              focus:outline-none focus:ring-4 focus:ring-primary-600/15 focus:border-primary-500 transition">
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ADRESSE (sans ville) --}}
                <div class="mt-4 rounded-xl border border-slate-200/70 dark:border-slate-700/70 p-4">
                    <h4 class="text-sm font-semibold text-slate-900 dark:text-slate-100 mb-3">Adresse</h4>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Rue / Quartier / Lot</label>

                        @if($isEditMode)
                            <input type="text" wire:model.blur="adresse" placeholder="Ex: Lot II M 45 Bis Analakely"
                                   class="w-full px-4 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600
                                          text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500
                                          focus:outline-none focus:ring-4 focus:ring-orange-600/15 focus:border-orange-500 transition">
                        @else
                            <input type="text" wire:model.blur="adresse" placeholder="Ex: Lot II M 45 Bis Analakely"
                                   class="w-full px-4 py-2.5 text-sm rounded-xl bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600
                                          text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500
                                          focus:outline-none focus:ring-4 focus:ring-primary-600/15 focus:border-primary-500 transition">
                        @endif
                    </div>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="rounded-xl border border-slate-200/70 dark:border-slate-700/70 bg-white dark:bg-slate-800 p-4">
                <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                    <button type="button"
                            wire:click="$set('nouveauPatient', false)"
                            class="inline-flex w-full sm:w-auto justify-center items-center gap-2 px-4 py-2.5 rounded-xl
                                   bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600
                                   text-slate-700 dark:text-slate-200 text-sm font-semibold transition">
                        <em class="ni ni-arrow-left"></em> Retour
                    </button>

                    @if($isEditMode)
                        <button type="button"
                                wire:click="validerNouveauPatient"
                                wire:loading.attr="disabled"
                                class="inline-flex w-full sm:w-auto justify-center items-center gap-2 px-5 py-2.5 rounded-xl
                                       bg-orange-600 hover:bg-orange-700 focus:ring-orange-600/20
                                       text-white text-sm font-semibold shadow-sm hover:shadow transition
                                       focus:outline-none focus:ring-4 disabled:opacity-50 disabled:cursor-not-allowed">
                            <em class="ni ni-save"></em>
                            <span wire:loading.remove wire:target="validerNouveauPatient">Enregistrer</span>
                            <span wire:loading wire:target="validerNouveauPatient" class="inline-flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Enregistrement…
                            </span>
                        </button>
                    @else
                        <button type="button"
                                wire:click="validerNouveauPatient"
                                wire:loading.attr="disabled"
                                class="inline-flex w-full sm:w-auto justify-center items-center gap-2 px-5 py-2.5 rounded-xl
                                       bg-primary-600 hover:bg-primary-700 focus:ring-primary-600/20
                                       text-white text-sm font-semibold shadow-sm hover:shadow transition
                                       focus:outline-none focus:ring-4 disabled:opacity-50 disabled:cursor-not-allowed">
                            <em class="ni ni-check"></em>
                            <span wire:loading.remove wire:target="validerNouveauPatient">Valider et continuer</span>
                            <span wire:loading wire:target="validerNouveauPatient" class="inline-flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Enregistrement…
                            </span>
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endif
