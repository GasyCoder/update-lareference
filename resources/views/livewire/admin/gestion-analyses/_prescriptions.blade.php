{{-- resources/views/livewire/admin/gestion-analyses/_prescriptions.blade.php --}}

<div class="overflow-x-auto">
    <table class="w-full text-sm text-left text-slate-600 dark:text-slate-200">
        <thead class="bg-gray-50 dark:bg-slate-800 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">
            <tr>
                <th class="px-6 py-4">Référence</th>
                <th class="px-6 py-4">Patient</th>
                <th class="px-6 py-4">Prescripteur</th>
                <th class="px-6 py-4">Analyses</th>
                <th class="px-6 py-4">Statut</th>
                <th class="px-6 py-4">Paiement</th>
                <th class="px-6 py-4">Date création</th>
                <th class="px-6 py-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $prescription)
                <tr wire:key="prescription-{{ $prescription->id }}"
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
                                    {{ $prescription->patient->telephone ?? 'Téléphone non renseigné' }}
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
                            <span class="text-slate-900 dark:text-slate-100">
                                {{ Str::limit('Dr. ' . ($prescription->prescripteur->nom ?? 'N/A'), 18) }}
                            </span>
                        </div>
                    </td>

                    {{-- Nombre d'analyses --}}
                    <td class="px-6 py-4 text-center">
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            {{ $prescription->analyses_count ?? 0 }}
                        </span>
                    </td>

                    {{-- Statut --}}
                    <td class="px-6 py-4 text-center">
                        <x-prescription-status :status="$prescription->status" />
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
                            <div class="flex items-center">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                    Aucun paiement
                                </span>
                            </div>
                        @endif
                    </td>

                    {{-- Date de création --}}
                    <td class="px-6 py-4 text-slate-500 dark:text-slate-400">
                        <div class="flex flex-col">
                            <span>{{ $prescription->created_at ? $prescription->created_at->format('d/m/Y') : 'N/A' }}</span>
                            <span
                                class="text-xs">{{ $prescription->created_at ? $prescription->created_at->format('H:i') : '' }}</span>
                        </div>
                    </td>

                    {{-- Actions --}}
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button wire:click="ouvrirFacture({{ $prescription->id }})"
                                class="inline-flex items-center justify-center w-8 h-8 text-indigo-600 bg-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-400 rounded-lg hover:bg-indigo-200 transition-colors"
                                title="Facture">
                                <em class="ni ni-file-text"></em>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-12 text-slate-500 dark:text-slate-400">
                        <div class="flex flex-col items-center">
                            <em class="ni ni-inbox text-4xl mb-4 text-slate-300 dark:text-slate-600"></em>
                            <p class="text-base font-medium">Aucune prescription trouvée</p>
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