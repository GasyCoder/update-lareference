{{-- resources/views/livewire/admin/gestion-analyses/_en-attente.blade.php --}}

<div class="p-4">
    {{-- En-tête spécifique --}}
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center space-x-3">
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                <em class="ni ni-clock mr-2"></em>
                {{ $data->total() }} prescription(s) en attente
            </span>
            <small class="text-gray-500 dark:text-gray-400 font-medium">En attente de prise en charge par un technicien</small>
        </div>
        <div class="flex space-x-2">
            <button class="inline-flex items-center justify-center w-9 h-9 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors" wire:click="$refresh" title="Rafraîchir">
                <em class="ni ni-reload"></em>
            </button>
        </div>
    </div>

    {{-- Alerte si beaucoup en attente --}}
    @if($data->total() > 10)
        <div class="flex items-center p-4 mb-4 text-sm text-yellow-800 dark:text-yellow-200 bg-yellow-100 dark:bg-yellow-900/50 rounded-xl border border-yellow-200 dark:border-yellow-800" role="alert">
            <em class="ni ni-alert-fill text-lg mr-3"></em>
            <div>
                <span class="font-bold">Attention !</span> {{ $data->total() }} prescriptions sont en attente de traitement.
            </div>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-slate-600 dark:text-slate-200">
            <thead class="bg-gray-50 dark:bg-slate-800 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="px-6 py-4">Référence</th>
                    <th class="px-6 py-4">Patient</th>
                    <th class="px-6 py-4">Prescripteur</th>
                    <th class="px-6 py-4">Analyses</th>
                    <th class="px-6 py-4">Paiement</th>
                    <th class="px-6 py-4">Priorité</th>
                    <th class="px-6 py-4">Attente</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $prescription)
                    @php
                        $heuresAttente = $prescription->created_at->diffInHours(now());
                        $priorite = $heuresAttente > 48 ? 'haute' : ($heuresAttente > 24 ? 'moyenne' : 'normale');
                    @endphp
                    <tr wire:key="en-attente-{{ $prescription->id }}" 
                        class="border-t border-gray-200 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors duration-200 {{ $heuresAttente > 48 ? 'bg-red-50/50 dark:bg-red-900/10' : ($heuresAttente > 24 ? 'bg-yellow-50/50 dark:bg-yellow-900/10' : '') }}">
                        {{-- Référence --}}
                        <td class="px-6 py-4 font-medium">{{ $prescription->reference }}</td>
                        
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

                        {{-- Prescripteur --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="relative flex-shrink-0 flex items-center justify-center text-xs text-white bg-primary-600 h-8 w-8 rounded-full font-medium">
                                    <span>{{ strtoupper(substr($prescription->prescripteur->nom ?? '', 0, 2)) }}</span>
                                </div>
                                <span class="text-slate-900 dark:text-slate-100 font-medium">
                                    {{ Str::limit('Dr. ' . ($prescription->prescripteur->nom ?? 'N/A'), 18) }}
                                </span>
                            </div>
                        </td>

                        {{-- Nombre d'analyses --}}
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ $prescription->analyses_count ?? 0 }}
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

                        {{-- Priorité --}}
                        <td class="px-6 py-4 text-center">
                            @switch($priorite)
                                @case('haute')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 animate-pulse">
                                        <em class="ni ni-arrow-up mr-1"></em>Haute
                                    </span>
                                    @break
                                @case('moyenne')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                        <em class="ni ni-minus mr-1"></em>Moyenne
                                    </span>
                                    @break
                                @default
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                        <em class="ni ni-arrow-down mr-1"></em>Normale
                                    </span>
                            @endswitch
                        </td>

                        {{-- Attente --}}
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-medium {{ $heuresAttente > 24 ? 'text-red-600 dark:text-red-400' : 'text-slate-600 dark:text-slate-400' }}">
                                    {{ $prescription->created_at->diffForHumans() }}
                                </span>
                                <small class="text-xs text-slate-400">{{ $prescription->created_at->format('H:i') }}</small>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button wire:click="openAssignModal({{ $prescription->id }})"
                                    class="inline-flex items-center justify-center w-8 h-8 text-orange-600 bg-orange-100 dark:bg-orange-900/30 dark:text-orange-400 rounded-lg hover:bg-orange-200 transition-colors"
                                    title="Assigner un technicien">
                                    <em class="ni ni-user-add"></em>
                                </button>
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
                                <em class="ni ni-check-circle text-5xl mb-4 text-green-500 dark:text-green-400"></em>
                                <p class="text-lg font-semibold text-slate-900 dark:text-slate-100">Aucune prescription en attente</p>
                                <p class="text-sm">Toutes les prescriptions ont été prises en charge.</p>
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
