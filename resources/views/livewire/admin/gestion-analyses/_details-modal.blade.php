{{-- resources/views/livewire/admin/gestion-analyses/_details-modal.blade.php --}}

@if($showDetailsModal && $selectedAnalyseId)
    @php
        $analyseDetail = \App\Models\Analyse::with(['patient', 'examen', 'prelevement'])->find($selectedAnalyseId);
    @endphp
    
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-flask me-2"></i>
                        Détails de l'analyse #{{ $analyseDetail->reference }}
                    </h5>
                    <button 
                        type="button" 
                        class="btn-close" 
                        wire:click="$set('showDetailsModal', false)"
                    ></button>
                </div>
                <div class="modal-body">
                    {{-- Informations patient --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <i class="fas fa-user me-2"></i>Patient
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Nom complet:</strong> 
                                    {{ $analyseDetail->patient->nom }} {{ $analyseDetail->patient->prenom }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Téléphone:</strong> 
                                    {{ $analyseDetail->patient->telephone }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Informations analyse --}}
                    <div class="card">
                        <div class="card-header bg-light">
                            <i class="fas fa-vial me-2"></i>Analyse
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Examen:</strong> {{ $analyseDetail->examen->nom ?? '-' }}</p>
                                    <p><strong>Prélèvement:</strong> {{ $analyseDetail->prelevement->nom ?? '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Statut:</strong> 
                                        <span class="badge bg-{{ $analyseDetail->statut === 'validee' ? 'success' : ($analyseDetail->statut === 'en_attente' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($analyseDetail->statut) }}
                                        </span>
                                    </p>
                                    <p><strong>Date création:</strong> {{ $analyseDetail->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button 
                        type="button" 
                        class="btn btn-secondary" 
                        wire:click="$set('showDetailsModal', false)"
                    >
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
