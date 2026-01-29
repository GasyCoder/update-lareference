{{-- resources/views/livewire/admin/gestion-analyses/_validees.blade.php --}}

<div class="p-4">
    {{-- En-tête spécifique --}}
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center space-x-3">
            <span
                class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                <em class="ni ni-check-circle-fill mr-2"></em>
                {{ $data->total() }} prescription(s) validée(s)
            </span>
            <small class="text-gray-500 dark:text-gray-400 font-medium">Résultats validés et prêts pour
                impression/envoi</small>
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
                    <th class="px-6 py-4">Prescripteur</th>
                    <th class="px-6 py-4 text-center">Analyses</th>
                    <th class="px-6 py-4">Paiement</th>
                    <th class="px-6 py-4">Validé par</th>
                    <th class="px-6 py-4">Date validation</th>
                    <th class="px-6 py-4 text-center">Résultats</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $prescription)
                    <tr wire:key="validee-{{ $prescription->id }}"
                        class="border-t border-gray-200 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors duration-200">
                        {{-- Référence --}}
                        <td class="px-6 py-4 font-medium">
                            <div class="flex items-center gap-2">
                                <em class="ni ni-check-circle text-green-500"></em>
                                {{ $prescription->reference }}
                            </div>
                        </td>

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

                        {{-- Prescripteur --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="relative flex-shrink-0 flex items-center justify-center text-xs text-white bg-primary-600 h-8 w-8 rounded-full font-medium">
                                    <span>{{ strtoupper(substr($prescription->prescripteur->nom ?? '', 0, 2)) }}</span>
                                </div>
                                <span class="text-slate-900 dark:text-slate-100 font-medium">
                                    {{ Str::limit('Dr. ' . ($prescription->prescripteur->nom ?? 'N/A'), 18) }}
                                </span>
                            </div>
                        </td>

                        {{-- Analyses --}}
                        <td class="px-6 py-4 text-center">
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                {{ $prescription->analyses_count }} validée(s)
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

                        {{-- Validé par --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div
                                    class="flex-shrink-0 h-8 w-8 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center font-bold text-xs">
                                    <em class="ni ni-user-check"></em>
                                </div>
                                <span
                                    class="text-xs font-medium text-slate-700 dark:text-slate-300">{{ $prescription->validated_by_name ?? 'Biologiste' }}</span>
                            </div>
                        </td>

                        {{-- Date validation --}}
                        <td class="px-6 py-4 border-b dark:border-slate-800">
                            <div class="flex flex-col text-xs">
                                <span
                                    class="text-slate-600 dark:text-slate-400 font-medium">{{ $prescription->updated_at->format('d/m/Y') }}</span>
                                <span class="text-slate-400">{{ $prescription->updated_at->format('H:i') }}</span>
                            </div>
                        </td>

                        {{-- Résultats --}}
                        <td class="px-6 py-4 text-center">
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold uppercase bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                Disponible
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button wire:click="ouvrirPDF({{ $prescription->id }})"
                                    class="inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-100 dark:bg-red-900/30 dark:text-red-400 rounded-lg hover:bg-red-200 transition-colors"
                                    title="Voir PDF">
                                    <em class="text-xl ni ni-file-pdf"></em>
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
                        <td colspan="9" class="text-center py-12 text-slate-500 dark:text-slate-400">
                            <div class="flex flex-col items-center">
                                <em class="ni ni-award text-5xl mb-4 text-green-500 dark:text-green-400"></em>
                                <p class="text-lg font-semibold text-slate-900 dark:text-slate-100">Aucune prescription
                                    validée</p>
                                <p class="text-base text-slate-500 dark:text-slate-400">Les prescriptions validées
                                    apparaîtront ici après validation finale.</p>
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