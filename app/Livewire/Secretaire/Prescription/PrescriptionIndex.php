<?php

namespace App\Livewire\Secretaire\Prescription;

use App\Models\Patient;
use Livewire\Component;
use App\Models\Paiement;
use App\Models\Prescription;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PrescriptionIndex extends Component
{
    use WithPagination, AuthorizesRequests;

    public $tab = 'actives';
    public $search = '';
    public $paymentFilter = null;

    // Modal properties
    public $showDeleteModal = false;
    public $showRestoreModal = false;
    public $showPermanentDeleteModal = false;
    public $showArchiveModal = false;
    public $showUnarchiveModal = false;
    public $showConfirmPaymentModal = false;
    public $showConfirmUnpaymentModal = false;
    public $selectedPrescriptionId = null;
    public $selectedPrescriptionForPayment = null;
    public $paymentAction = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'tab' => ['except' => 'actives'],
        'paymentFilter' => ['except' => null],
    ];

    public const STATUS_LABELS = [
        'EN_ATTENTE' => 'En attente',
        'EN_COURS' => 'En cours',
        'TERMINE' => 'Terminé',
        'VALIDE' => 'Validé',
        'A_REFAIRE' => 'À refaire',
        'ARCHIVE' => 'Archivé',
    ];

    protected $listeners = [
        'prescriptionAdded' => '$refresh',
    ];

    public function mount()
    {
        $this->tab = request()->query('tab', 'actives');
    }

    public function switchTab($tab)
    {
        $this->tab = $tab;
        $this->paymentFilter = null;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->resetPage();
    }

    // =====================================
    // 🚀 COMPUTED PROPERTIES (OPTIMISÉ)
    // =====================================

    /**
     * Statistiques globales (cache fichier de 10 minutes)
     */
    #[Computed]
    public function stats()
    {
        return Cache::remember('prescription_stats_v6', 120, function () {
            // UNE SEULE requête au lieu de multiples
            $stats = DB::select("
                SELECT 
                    COUNT(CASE WHEN status = 'EN_ATTENTE' AND deleted_at IS NULL THEN 1 END) as en_attente,
                    COUNT(CASE WHEN status = 'EN_COURS' AND deleted_at IS NULL THEN 1 END) as en_cours,
                    COUNT(CASE WHEN status = 'TERMINE' AND deleted_at IS NULL THEN 1 END) as termine,
                    COUNT(CASE WHEN status = 'VALIDE' AND deleted_at IS NULL THEN 1 END) as valide,
                    COUNT(CASE WHEN status = 'ARCHIVE' AND deleted_at IS NULL THEN 1 END) as archive,
                    COUNT(CASE WHEN deleted_at IS NOT NULL THEN 1 END) as deleted
                FROM prescriptions
            ")[0];

            // ✅ CORRECTION : Exclure les paiements soft deleted
            $paymentStats = DB::select("
                SELECT 
                    COUNT(CASE WHEN paiements.status = 1 THEN 1 END) as paye,
                    COUNT(CASE WHEN paiements.status = 0 THEN 1 END) as non_paye
                FROM paiements
                INNER JOIN prescriptions ON paiements.prescription_id = prescriptions.id
                WHERE prescriptions.deleted_at IS NULL
                AND paiements.deleted_at IS NULL
            ")[0];

            $actives = $stats->en_attente + $stats->en_cours + $stats->termine;
            $totalGlobal = $actives + $stats->valide;
            $completed = $stats->termine + $stats->valide;

            return [
                'countEnAttente' => (int)$stats->en_attente,
                'countEnCours' => (int)$stats->en_cours,
                'countTermine' => (int)$stats->termine,
                'countValide' => (int)$stats->valide,
                'countArchive' => (int)$stats->archive,
                'countDeleted' => (int)$stats->deleted,
                'countPaye' => (int)$paymentStats->paye,
                'countNonPaye' => (int)$paymentStats->non_paye,
                'countActives' => $actives,
                'tauxProgression' => $actives > 0 ? round(($stats->termine / $actives) * 100, 1) : 0,
                'tauxEfficacite' => $totalGlobal > 0 ? round(($completed / $totalGlobal) * 100, 1) : 0,
                'tauxPaiement' => ($paymentStats->paye + $paymentStats->non_paye) > 0 
                    ? round(($paymentStats->paye / ($paymentStats->paye + $paymentStats->non_paye)) * 100, 2) 
                    : 0
            ];
        });
    }

    /**
     * Prescriptions actives (lazy loaded)
     */
    #[Computed]
    public function activePrescriptions()
    {
        if ($this->tab !== 'actives') {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }

        $prescriptions = $this->buildQuery(['EN_ATTENTE', 'EN_COURS', 'TERMINE'])
            ->latest()
            ->paginate(10); // ✅ 10 au lieu de 15

        $this->addStatusLabels($prescriptions);
        return $prescriptions;
    }

    #[Computed]
    public function validePrescriptions()
    {
        if ($this->tab !== 'valide') {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }

        $prescriptions = $this->buildQuery(['VALIDE'])
            ->latest()
            ->paginate(10, ['*'], 'valide_page');

        $this->addStatusLabels($prescriptions);
        return $prescriptions;
    }

    #[Computed]
    public function deletedPrescriptions()
    {
        if ($this->tab !== 'deleted') {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }

        $prescriptions = $this->buildQuery([], true)
            ->latest()
            ->paginate(10, ['*'], 'deleted_page');

        $this->addStatusLabels($prescriptions);
        return $prescriptions;
    }
    // =====================================
    // 🔧 MÉTHODES UTILITAIRES
    // =====================================

    /**
     * Construire la requête de base (VERSION CORRIGÉE - FINALE)
     */
    protected function buildQuery(array $statuses = [], bool $trashed = false)
    {
        $query = Prescription::query()
            ->select([
                'prescriptions.id',
                'prescriptions.reference',
                'prescriptions.status',
                'prescriptions.patient_id',
                'prescriptions.prescripteur_id',
                'prescriptions.created_at',
            ])
            ->with([
                'patient:id,nom,prenom,telephone',
                'prescripteur:id,nom',
                'paiements:id,prescription_id,status,montant,date_paiement'
            ])
            ->withCount('analyses')
            ->whereHas('patient', fn($q) => $q->whereNull('deleted_at'));

        // ✅ SI UN FILTRE DE PAIEMENT EST ACTIF : IGNORER les filtres de statut
        if ($this->paymentFilter) {
            // Appliquer UNIQUEMENT le filtre de paiement
            switch ($this->paymentFilter) {
                case 'paye':
                    $query->whereHas('paiements', fn($q) => $q->where('status', 1));
                    break;
                case 'non_paye':
                    $query->whereHas('paiements', fn($q) => $q->where('status', 0));
                    break;
                case 'sans_paiement':
                    $query->doesntHave('paiements');
                    break;
            }
            
            // ✅ NE PAS appliquer les filtres de statut/corbeille quand un filtre de paiement est actif
            // On affiche TOUTES les prescriptions (tous statuts) qui correspondent au filtre de paiement
            
        } else {
            // ✅ SINON : Appliquer les filtres normaux de statut/corbeille
            if ($trashed) {
                $query->onlyTrashed();
            } elseif (!empty($statuses)) {
                $query->whereIn('prescriptions.status', $statuses);
            }
        }

        // Recherche (toujours appliquée)
        if ($this->search) {
            $search = '%' . $this->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('prescriptions.reference', 'like', $search)
                ->orWhereHas('patient', function($q) use ($search) {
                    $q->where('nom', 'like', $search)
                        ->orWhere('prenom', 'like', $search)
                        ->orWhere('telephone', 'like', $search);
                });
            });
        }

        return $query;
    }

    /**
     * Ajouter les labels de statut
     */
    private function addStatusLabels($prescriptions)
    {
        $prescriptions->getCollection()->transform(function ($prescription) {
            $prescription->status_label = self::STATUS_LABELS[$prescription->status] ?? $prescription->status;
            return $prescription;
        });
    }

    /**
     * Rafraîchir les statistiques (invalider le cache)
     */
    public function refreshCounts()
    {
        // ✅ Vider TOUTES les versions du cache
        Cache::forget('prescription_stats_v4');
        Cache::forget('prescription_stats_v5');
        Cache::forget('prescription_stats_v6'); // ✅ Nouvelle version
        
        // ✅ Forcer le recalcul
        unset($this->stats);
        
        // ✅ Dispatch avec les nouvelles stats
        $this->dispatch('updateCounts', $this->stats);
    }

    /**
     * Forcer le rafraîchissement immédiat des statistiques
     */
    public function forceRefreshStats()
    {
        // Vider tous les caches de stats
        Cache::forget('prescription_stats_v4');
        Cache::forget('prescription_stats_v5');
        
        // Forcer le recalcul
        unset($this->stats);
        
        session()->flash('success', 'Statistiques rafraîchies avec succès !');
    }

    /**
     * Rafraîchir uniquement les compteurs de paiement
     */
    public function refreshPaymentCounts()
    {
        $this->refreshCounts();
    }

    /**
     * Rafraîchir le compteur d'archives
     */
    public function refreshArchiveCount()
    {
        $this->refreshCounts();
    }

    /**
     * Get count of active prescriptions
     */
    public function getCountActivesProperty()
    {
        return $this->stats['countActives'];
    }

    /**
     * Get progression statistics
     */
    public function getProgressionStats()
    {
        return [
            'totalActives' => $this->stats['countActives'],
            'termine' => $this->stats['countTermine'],
            'tauxProgression' => $this->stats['tauxProgression']
        ];
    }

    /**
     * Get efficiency statistics
     */
    public function getEfficiencyStats()
    {
        return [
            'totalGlobal' => $this->stats['countActives'] + $this->stats['countValide'],
            'completed' => $this->stats['countTermine'] + $this->stats['countValide'],
            'tauxEfficacite' => $this->stats['tauxEfficacite']
        ];
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStats()
    {
        return [
            'paye' => $this->stats['countPaye'],
            'nonPaye' => $this->stats['countNonPaye'],
            'total' => $this->stats['countPaye'] + $this->stats['countNonPaye'],
            'tauxPaiement' => $this->stats['tauxPaiement']
        ];
    }

    // =====================================
    // 💰 GESTION DES PAIEMENTS
    // =====================================

    public function filterByPaymentStatus($status)
    {
        if ($status === 'tous' || $status === $this->paymentFilter) {
            $this->paymentFilter = null;
        } else {
            $this->paymentFilter = $status;
        }
        $this->resetPage();
    }

    public function clearPaymentFilter()
    {
        $this->paymentFilter = null;
        $this->resetPage();
    }

    public function togglePaiementStatus($prescriptionId)
    {
        try {
            $paiement = Paiement::whereHas('prescription', fn($q) => $q->where('id', $prescriptionId))
                ->first();
            
            if (!$paiement) {
                session()->flash('error', 'Aucun paiement trouvé.');
                return;
            }
            
            if ($paiement->status) {
                $this->confirmUnpayment($prescriptionId);
            } else {
                $this->confirmPayment($prescriptionId);
            }
            
        } catch (\Exception $e) {
            Log::error('Erreur toggle paiement', ['error' => $e->getMessage()]);
            session()->flash('error', 'Erreur lors de la vérification du statut.');
        }
    }

    public function confirmPayment($prescriptionId)
    {
        $this->selectedPrescriptionForPayment = $prescriptionId;
        $this->paymentAction = 'pay';
        $this->showConfirmPaymentModal = true;
    }

    public function confirmUnpayment($prescriptionId)
    {
        $this->selectedPrescriptionForPayment = $prescriptionId;
        $this->paymentAction = 'unpay';
        $this->showConfirmUnpaymentModal = true;
    }

    public function executeMarquerCommePayé()
    {
        try {
            if (!$this->selectedPrescriptionForPayment) {
                $this->resetModal();
                return;
            }

            $paiement = Paiement::whereHas('prescription', fn($q) => 
                $q->where('id', $this->selectedPrescriptionForPayment)
            )->firstOrFail();
            
            $paiement->changerStatutPaiement(true);
            
            session()->flash('success', 'Paiement marqué comme payé.');
            $this->refreshCounts();
            $this->resetModal();
            
        } catch (\Exception $e) {
            Log::error('Erreur marquage paiement payé', ['error' => $e->getMessage()]);
            session()->flash('error', 'Erreur lors du marquage.');
            $this->resetModal();
        }
    }

    public function executeMarquerCommeNonPayé()
    {
        try {
            if (!$this->selectedPrescriptionForPayment) {
                $this->resetModal();
                return;
            }

            $paiement = Paiement::whereHas('prescription', fn($q) => 
                $q->where('id', $this->selectedPrescriptionForPayment)
            )->firstOrFail();
            
            $paiement->changerStatutPaiement(false);
            
            session()->flash('success', 'Paiement marqué comme non payé.');
            $this->refreshCounts();
            $this->resetModal();
            
        } catch (\Exception $e) {
            Log::error('Erreur marquage paiement non payé', ['error' => $e->getMessage()]);
            session()->flash('error', 'Erreur lors du marquage.');
            $this->resetModal();
        }
    }

    // =====================================
    // 🗑️ GESTION DES ACTIONS
    // =====================================

    public function confirmDelete($prescriptionId)
    {
        $this->selectedPrescriptionId = $prescriptionId;
        $this->showDeleteModal = true;
    }

    public function deletePrescription()
    {
        try {
            if (!$this->selectedPrescriptionId) {
                return;
            }

            Prescription::findOrFail($this->selectedPrescriptionId)->delete();
            session()->flash('success', 'Prescription mise en corbeille.');
            $this->refreshCounts();
            $this->resetModal();
        } catch (\Exception $e) {
            Log::error('Erreur suppression', ['error' => $e->getMessage()]);
            session()->flash('error', 'Erreur lors de la suppression.');
            $this->resetModal();
        }
    }

    public function confirmRestore($prescriptionId)
    {
        $this->selectedPrescriptionId = $prescriptionId;
        $this->showRestoreModal = true;
    }

    public function restorePrescription()
    {
        try {
            if (!$this->selectedPrescriptionId) {
                return;
            }

            Prescription::withTrashed()->findOrFail($this->selectedPrescriptionId)->restore();
            session()->flash('success', 'Prescription restaurée.');
            $this->refreshCounts();
            $this->resetModal();
        } catch (\Exception $e) {
            Log::error('Erreur restauration', ['error' => $e->getMessage()]);
            session()->flash('error', 'Erreur lors de la restauration.');
            $this->resetModal();
        }
    }

    public function confirmPermanentDelete($prescriptionId)
    {
        if (!Auth::user()->isAdmin()) {
            session()->flash('error', 'Seuls les administrateurs peuvent supprimer définitivement.');
            return;
        }

        $this->selectedPrescriptionId = $prescriptionId;
        $this->showPermanentDeleteModal = true;
    }

    public function permanentDeletePrescription()
    {
        try {
            if (!Auth::user()->isAdmin()) {
                session()->flash('error', 'Action non autorisée.');
                return;
            }

            if (!$this->selectedPrescriptionId) {
                return;
            }

            Prescription::withTrashed()->findOrFail($this->selectedPrescriptionId)->forceDelete();
            session()->flash('success', 'Prescription supprimée définitivement.');
            $this->refreshCounts();
            $this->resetModal();
        } catch (\Exception $e) {
            Log::error('Erreur suppression définitive', ['error' => $e->getMessage()]);
            session()->flash('error', 'Erreur lors de la suppression définitive.');
            $this->resetModal();
        }
    }

    public function confirmArchive($prescriptionId)
    {
        $this->selectedPrescriptionId = $prescriptionId;
        $this->showArchiveModal = true;
    }

    public function archivePrescription()
    {
        try {
            if (!$this->selectedPrescriptionId) {
                return;
            }

            $prescription = Prescription::findOrFail($this->selectedPrescriptionId);

            if ($prescription->status === 'VALIDE') {
                $prescription->update(['status' => 'ARCHIVE']);
                session()->flash('success', 'Prescription archivée.');
                $this->refreshCounts();
            } else {
                session()->flash('error', 'Seules les prescriptions validées peuvent être archivées.');
            }

            $this->resetModal();
        } catch (\Exception $e) {
            Log::error('Erreur archivage', ['error' => $e->getMessage()]);
            session()->flash('error', 'Erreur lors de l\'archivage.');
            $this->resetModal();
        }
    }

    public function confirmUnarchive($prescriptionId)
    {
        $this->selectedPrescriptionId = $prescriptionId;
        $this->showUnarchiveModal = true;
    }

    public function unarchivePrescription()
    {
        try {
            if (!$this->selectedPrescriptionId) {
                return;
            }

            Prescription::findOrFail($this->selectedPrescriptionId)->update(['status' => 'VALIDE']);
            session()->flash('success', 'Prescription désarchivée.');
            $this->refreshCounts();
            $this->resetModal();
        } catch (\Exception $e) {
            Log::error('Erreur désarchivage', ['error' => $e->getMessage()]);
            session()->flash('error', 'Erreur lors du désarchivage.');
            $this->resetModal();
        }
    }

    public function resetModal()
    {
        $this->showDeleteModal = false;
        $this->showRestoreModal = false;
        $this->showPermanentDeleteModal = false;
        $this->showArchiveModal = false;
        $this->showUnarchiveModal = false;
        $this->showConfirmPaymentModal = false;
        $this->showConfirmUnpaymentModal = false;
        $this->selectedPrescriptionId = null;
        $this->selectedPrescriptionForPayment = null;
        $this->paymentAction = null;
    }

    public function edit($prescriptionId)
    {
        $this->dispatch('editPrescription', $prescriptionId);
    }

    public function render()
    {
        $stats = $this->stats;
        
        return view('livewire.secretaire.prescription.prescription-index', [
            'activePrescriptions' => $this->activePrescriptions,
            'validePrescriptions' => $this->validePrescriptions,
            'deletedPrescriptions' => $this->deletedPrescriptions,
            // Passer toutes les stats individuellement pour compatibilité avec les vues
            'countArchive' => $stats['countArchive'],
            'countEnAttente' => $stats['countEnAttente'],
            'countEnCours' => $stats['countEnCours'],
            'countTermine' => $stats['countTermine'],
            'countValide' => $stats['countValide'],
            'countDeleted' => $stats['countDeleted'],
            'countPaye' => $stats['countPaye'],
            'countNonPaye' => $stats['countNonPaye'],
            'countActives' => $stats['countActives'],
            'paymentStats' => $this->getPaymentStats(),
            'progressionStats' => $this->getProgressionStats(),
            'efficiencyStats' => $this->getEfficiencyStats(),
        ]);
    }
}