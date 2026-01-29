{{-- resources/views/livewire/admin/gestion-analyses/_en-cours.blade.php --}}

<div class="p-4">
    {{-- En-tête spécifique --}}
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center space-x-3">
            <span
                class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                <em class="ni ni-loading mr-2 animate-spin"></em>
                {{ $data->total() }} prescription(s) en cours
            </span>
            <small class="text-gray-500 dark:text-gray-400 font-medium">Analyses en cours de réalisation par les
                techniciens</small>
        </div>
        <div class="flex space-x-2">
            <button
                class="inline-flex items-center justify-center w-9 h-9 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
                wire:click="$refresh" title="Rafraîchir">
                <em class="ni ni-reload"></em>
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-slate-600 dark:text-slate-200">
            <thead
                class="bg-gray-50 dark:bg-slate-800 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="px-6 py-4">Référence</th>
                    <th class="px-6 py-4">Patient</th>
                    <th class="px-6 py-4">Technicien</th>
                    <th class="px-6 py-4">Progression</th>
                    <th class="px-6 py-4">Analyses</th>
                    <th class="px-6 py-4">Paiement</th>
                    <th class="px-6 py-4">Démarré</th>
                    <th class="px-6 py-4">Durée</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $prescription)
                    @php
                        $heuresEnCours = $prescription->updated_at->diffInHours(now());
                        $analysesTerminees = $prescription->analyses()->wherePivot('status', 'TERMINE')->count();
                        $progression = $prescription->analyses_count > 0
                            ? round(($analysesTerminees / $prescription->analyses_count) * 100)
                            : 0;
                    @endphp
                    <tr wire:key="en-cours-{{ $prescription->id }}"
                        class="border-t border-gray-200 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors duration-200">
                        {{-- Référence --}}
                        <td class="px-6 py-4 font-medium">{{ $prescription->reference }}</td>

                        {{-- Patient --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="relative flex-shrink-0 flex items-center justify-center text-xs text-white bg-green-600 h-8 w-8 rounded-full font-medium">
                                    <span>{{ strtoupper(substr($prescription->patient->nom ?? 'N', 0, 1) . substr($prescription->patient->prenom ?? 'A', 0, 1)) }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-medium text-slate-900 dark:text-slate-100">
                                        {{ Str::limit(($prescription->patient->nom ?? 'N/A') . ' ' . ($prescription->patient->prenom ?? ''), 18) }}
                                    </span>
                                    <span class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $prescription->patient->telephone ?? '-' }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        {{-- Technicien --}}
                        <td class="px-6 py-4">
                            @if($prescription->technicien)
                                <div class="flex items-center gap-2">
                                    <div
                                        class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-800 dark:text-indigo-400 flex items-center justify-center font-bold text-xs border border-indigo-200 dark:border-indigo-800">
                                        {{ strtoupper(substr($prescription->technicien->name ?? 'T', 0, 1)) }}
                                    </div>
                                    <span
                                        class="text-slate-700 dark:text-slate-300 font-medium text-xs">{{ $prescription->technicien->name }}</span>
                                </div>
                            @else
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                    Non assigné
                                </span>
                            @endif
                        </td>

                        {{-- Progression --}}
                        <td class="px-6 py-4" style="min-width: 140px;">
                            <div class="flex flex-col gap-1.5">
                                <div class="w-full bg-gray-200 dark:bg-slate-700 rounded-full h-2">
                                    <div class="bg-blue-600 dark:bg-blue-500 h-2 rounded-full transition-all duration-500"
                                        style="width: {{ $progression }}%">
                                    </div>
                                </div>
                                <div class="flex justify-between items-center text-[10px]">
                                    <span class="text-slate-500 dark:text-slate-400 font-medium">{{ $progression }}%
                                        terminé</span>
                                    <span
                                        class="text-slate-400">{{ $analysesTerminees }}/{{ $prescription->analyses_count }}</span>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-900 dark:text-slate-300">
                                {{ $prescription->analyses_count }}
                            </span>
                        </td>

                        {{-- Statut Paiement --}}
                        <td class="px-6 py-4">
                            @php
                                $paiement = $prescription->paiements->first();
                                $estPaye = $paiement ? $paiement->status : false;
                            @endphp
                            @if ($paiement)
                                <div class="flex items-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $estPaye ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                        <em class="ni {{ $estPaye ? 'ni-check-circle' : 'ni-alert-circle' }} mr-1"></em>
                                        {{ $estPaye ? 'Payé' : 'Non Payé' }}
                                    </span>
                                </div>
                            @else
                                <div class="flex items-center justify-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-900 dark:text-slate-200">
                                        Aucun
                                    </span>
                                </div>
                            @endif
                        </td>

                        {{-- Démarré le --}}
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-slate-600 dark:text-slate-400 text-xs">
                                    {{ $prescription->updated_at->format('d/m/Y') }}
                                </span>
                                <span class="text-[10px] text-slate-400">
                                    {{ $prescription->updated_at->format('H:i') }}
                                </span>
                            </div>
                        </td>

                        {{-- Durée --}}
                        <td class="px-6 py-4 text-center">
                            @if($heuresEnCours > 24)
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 animate-pulse">
                                    <em class="ni ni-clock mr-1"></em>{{ $heuresEnCours }}h
                                </span>
                            @elseif($heuresEnCours > 8)
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                    <em class="ni ni-clock mr-1"></em>{{ $heuresEnCours }}h
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    <em class="ni ni-clock-fill mr-1"></em>{{ $heuresEnCours }}h
                                </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button wire:click="showDetails({{ $prescription->id }})"
                                    class="inline-flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 rounded-lg hover:bg-blue-200 transition-colors"
                                    title="Voir détails">
                                    <em class="ni ni-eye"></em>
                                </button>
                                <button wire:click="openChangeStatusModal({{ $prescription->id }})"
                                    class="inline-flex items-center justify-center w-8 h-8 text-green-600 bg-green-100 dark:bg-green-900/30 dark:text-green-400 rounded-lg hover:bg-green-200 transition-colors"
                                    title="Changer statut">
                                    <em class="ni ni-edit"></em>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-12 text-slate-500 dark:text-slate-400">
                            <div class="flex flex-col items-center">
                                <em class="ni ni-flask text-5xl mb-4 text-blue-500 dark:text-blue-400"></em>
                                <p class="text-lg font-semibold text-slate-900 dark:text-slate-100">Aucune analyse en cours
                                </p>
                                <p class="text-base">Aucune prescription n'est actuellement en cours de traitement.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($data->hasPages())
        <div class="p-6 border-t border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-800/50">
            {{ $data->links() }}
        </div>
    @endif
</div>