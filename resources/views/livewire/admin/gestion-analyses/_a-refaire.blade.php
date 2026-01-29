{{-- resources/views/livewire/admin/gestion-analyses/_a-refaire.blade.php --}}

<div class="p-4">
    {{-- En-tête spécifique --}}
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center space-x-3">
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                <em class="ni ni-redo mr-2"></em>
                {{ $data->total() }} prescription(s) à refaire
            </span>
            <small class="text-gray-500 dark:text-gray-400 font-medium">Analyses nécessitant une reprise</small>
        </div>
        <div class="flex space-x-2">
            <button class="inline-flex items-center justify-center w-9 h-9 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors" wire:click="$refresh" title="Rafraîchir">
                <em class="ni ni-reload"></em>
            </button>
        </div>
    </div>

    {{-- Alerte importante --}}
    @if($data->total() > 0)
        <div class="flex items-start p-4 mb-4 text-sm text-red-800 dark:text-red-200 bg-red-100 dark:bg-red-900/40 rounded-lg border border-red-200 dark:border-red-800" role="alert">
            <em class="ni ni-alert-fill mr-3 text-lg mt-0.5"></em>
            <div>
                <span class="font-bold">Attention !</span> Ces prescriptions nécessitent une intervention immédiate. Veuillez vérifier les motifs et reprendre les analyses concernées.
            </div>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-slate-600 dark:text-slate-200">
            <thead class="bg-gray-50 dark:bg-slate-800 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="px-6 py-4">Référence</th>
                    <th class="px-6 py-4">Patient</th>
                    <th class="px-6 py-4">Analyses</th>
                    <th class="px-6 py-4">Paiement</th>
                    <th class="px-6 py-4">Motif</th>
                    <th class="px-6 py-4">Technicien</th>
                    <th class="px-6 py-4">Retard</th>
                    <th class="px-6 py-4 text-center">Urgence</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $prescription)
                    @php
                        $joursDepuis = $prescription->updated_at->diffInDays(now());
                        $urgence = $joursDepuis > 3 ? 'critique' : ($joursDepuis > 1 ? 'haute' : 'normale');
                    @endphp
                    <tr wire:key="a-refaire-{{ $prescription->id }}"
                        class="border-t border-gray-200 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors duration-200 {{ $urgence === 'critique' ? 'bg-red-50/50 dark:bg-red-900/10' : '' }}">
                        {{-- Référence --}}
                        <td class="px-6 py-4 font-medium text-red-600 dark:text-red-400">
                            <div class="flex items-center gap-2">
                                <em class="ni ni-alert-circle"></em>
                                {{ $prescription->reference }}
                            </div>
                        </td>
                        
                        {{-- Patient --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="relative flex-shrink-0 flex items-center justify-center text-xs text-white bg-green-600 h-8 w-8 rounded-full font-medium">
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

                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                {{ $prescription->analyses_count }} à refaire
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
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $estPaye ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                        <em class="ni {{ $estPaye ? 'ni-check-circle' : 'ni-alert-circle' }} mr-1"></em>
                                        {{ $estPaye ? 'Payé' : 'Non Payé' }}
                                    </span>
                                </div>
                            @else
                                <div class="flex items-center justify-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-900 dark:text-slate-200">
                                        Aucun
                                    </span>
                                </div>
                            @endif
                        </td>

                        {{-- Motif --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 max-w-xs overflow-hidden">
                                <em class="ni ni-chat-msg text-slate-400"></em>
                                <span class="text-xs text-slate-600 dark:text-slate-400 truncate" title="{{ $prescription->commentaire_admin ?? $prescription->motif_refaire ?? 'Non spécifié' }}">
                                    {{ Str::limit($prescription->commentaire_admin ?? $prescription->motif_refaire ?? 'Non spécifié', 30) }}
                                </span>
                            </div>
                        </td>

                        {{-- Technicien --}}
                        <td class="px-6 py-4">
                            @if($prescription->technicien)
                                <div class="flex items-center gap-2">
                                    <div class="flex-shrink-0 h-6 w-6 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 flex items-center justify-center font-bold text-[10px]">
                                        {{ strtoupper(substr($prescription->technicien->name ?? 'T', 0, 1)) }}
                                    </div>
                                    <span class="text-xs text-slate-700 dark:text-slate-300">{{ $prescription->technicien->name }}</span>
                                </div>
                            @else
                                <span class="text-slate-400 text-[10px] italic">Non assigné</span>
                            @endif
                        </td>

                        {{-- Retard --}}
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-medium {{ $joursDepuis > 1 ? 'text-red-600 dark:text-red-400' : 'text-slate-600 dark:text-slate-400' }}">
                                    {{ $prescription->updated_at->diffForHumans() }}
                                </span>
                                <span class="text-[10px] text-slate-400">
                                    {{ $prescription->updated_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </td>

                        {{-- Urgence --}}
                        <td class="px-6 py-4 text-center">
                            @switch($urgence)
                                @case('critique')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold uppercase bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 animate-pulse">
                                        Critique
                                    </span>
                                    @break
                                @case('haute')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold uppercase bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                        Haute
                                    </span>
                                    @break
                                @default
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-700 dark:bg-slate-900/30 dark:text-slate-400">
                                        Normale
                                    </span>
                            @endswitch
                        </td>

                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button wire:click="ouvrirFacture({{ $prescription->id }})"
                                    class="inline-flex items-center justify-center w-8 h-8 text-indigo-600 bg-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-400 rounded-lg hover:bg-indigo-200 transition-colors"
                                    title="Facture">
                                    <em class="ni ni-file-text"></em>
                                </button>
                                <button wire:click="showDetails({{ $prescription->id }})"
                                    class="inline-flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 rounded-lg hover:bg-blue-200 transition-colors"
                                    title="Voir détails">
                                    <em class="ni ni-eye"></em>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-12 text-slate-500 dark:text-slate-400">
                            <div class="flex flex-col items-center">
                                <em class="ni ni-happy text-5xl mb-4 text-green-500 dark:text-green-400"></em>
                                <p class="text-lg font-semibold text-slate-900 dark:text-slate-100">Excellent ! Aucune analyse à refaire</p>
                                <p class="text-base text-slate-500 dark:text-slate-400">Toutes les analyses ont été réalisées ou validées correctement.</p>
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
