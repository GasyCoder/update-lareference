<?php
// app/Livewire/Admin/GestionAnalyses.php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Prescription;
use App\Models\Prescripteur;
use App\Models\User;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class GestionAnalyses extends Component
{
    use WithPagination, AuthorizesRequests;

    // =====================================
    // 📌 PROPRIÉTÉS
    // =====================================

    public string $activeTab = 'prescriptions';
    public string $search = '';
    public string $dateFilter = '';
    public string $prescripteurFilter = '';
    public string $dateDebut = '';
    public string $dateFin = '';
    public string $paymentStatusFilter = '';
    public string $technicienFilter = '';
    public bool $showAdvancedFilters = false;

    // Tri
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    // Modals
    public bool $showDetailsModal = false;
    public bool $showChangeStatusModal = false;
    public bool $showAssignModal = false;
    public bool $showConfirmPaymentModal = false;
    public bool $showConfirmUnpaymentModal = false;
    public ?int $selectedPrescriptionId = null;
    public ?int $selectedPrescriptionForPayment = null;
    public ?int $technicienId = null;
    public ?string $newStatus = null;
    public ?string $commentaire = null;
    public ?string $paymentAction = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'activeTab' => ['except' => 'prescriptions'],
        'dateFilter' => ['except' => ''],
    ];

    public array $statusLabels = [
        'EN_ATTENTE' => ['label' => 'En attente', 'color' => 'warning', 'icon' => 'clock'],
        'EN_COURS' => ['label' => 'En cours', 'color' => 'info', 'icon' => 'spinner'],
        'TERMINE' => ['label' => 'Terminé', 'color' => 'primary', 'icon' => 'check-circle'],
        'VALIDE' => ['label' => 'Validé', 'color' => 'success', 'icon' => 'check-double'],
        'A_REFAIRE' => ['label' => 'À refaire', 'color' => 'danger', 'icon' => 'redo'],
    ];

    public array $tabs = [
        'prescriptions' => ['label' => 'Prescriptions', 'icon' => 'list'],
        'en_attente' => ['label' => 'En attente', 'icon' => 'clock'],
        'en_cours' => ['label' => 'En cours', 'icon' => 'spinner'],
        'termine' => ['label' => 'Terminées', 'icon' => 'check-circle'],
        'validees' => ['label' => 'Validées', 'icon' => 'check-double'],
        'a_refaire' => ['label' => 'À refaire', 'icon' => 'redo'],
    ];

    protected $paginationTheme = 'tailwind';

    // =====================================
    // 🔄 LIFECYCLE
    // =====================================

    public function mount()
    {
        $this->activeTab = request()->query('tab', 'prescriptions');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingActiveTab()
    {
        $this->resetPage();
    }

    public function refreshStats()
    {
        Cache::forget('admin_prescription_stats');
        $this->dispatch('stats-refreshed');
    }

    public function exportTab()
    {
        $filename = 'export-analyses-' . $this->activeTab . '-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $statusMap = [
            'en_attente' => ['EN_ATTENTE'],
            'en_cours' => ['EN_COURS'],
            'termine' => ['TERMINE'],
            'validees' => ['VALIDE'],
            'a_refaire' => ['A_REFAIRE'],
            'toutes' => ['EN_ATTENTE', 'EN_COURS', 'TERMINE'],
        ];

        $statuses = $statusMap[$this->activeTab] ?? ['EN_ATTENTE', 'EN_COURS', 'TERMINE'];

        $query = $this->getBaseQuery()->whereIn('prescriptions.status', $statuses);

        return response()->streamDownload(function () use ($query) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel
            fputs($file, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

            // CSV Header
            fputcsv($file, [
                'Référence',
                'Date',
                'Patient',
                'Téléphone',
                'Prescripteur',
                'Statut',
                'Analyses',
                'Paiement',
                'Montant'
            ], ';');

            $query->chunk(100, function ($prescriptions) use ($file) {
                foreach ($prescriptions as $p) {
                    $paiement = $p->paiements->first();
                    $statutPaiement = $p->paiements->where('status', 1)->isNotEmpty() ? 'Payé' : 'Non Payé';
                    $montant = $paiement ? $paiement->montant : 0;

                    fputcsv($file, [
                        $p->reference,
                        $p->created_at->format('d/m/Y H:i'),
                        ($p->patient->nom ?? '') . ' ' . ($p->patient->prenom ?? ''),
                        $p->patient->telephone ?? '',
                        'Dr. ' . ($p->prescripteur->nom ?? ''),
                        $p->status,
                        $p->analyses_count,
                        $statutPaiement,
                        $montant
                    ], ';');
                }
            });

            fclose($file);
        }, $filename, $headers);
    }

    // =====================================
    // 📊 STATISTIQUES
    // =====================================

    #[Computed]
    public function stats()
    {
        return Cache::remember('admin_prescription_stats', 120, function () {
            // Stats de statut
            $stats = DB::select("
                SELECT 
                    COUNT(*) as total,
                    COUNT(CASE WHEN status = 'EN_ATTENTE' AND deleted_at IS NULL THEN 1 END) as en_attente,
                    COUNT(CASE WHEN status = 'EN_COURS' AND deleted_at IS NULL THEN 1 END) as en_cours,
                    COUNT(CASE WHEN status = 'TERMINE' AND deleted_at IS NULL THEN 1 END) as termine,
                    COUNT(CASE WHEN status = 'VALIDE' AND deleted_at IS NULL THEN 1 END) as valide,
                    COUNT(CASE WHEN status = 'A_REFAIRE' AND deleted_at IS NULL THEN 1 END) as a_refaire
                FROM prescriptions
                WHERE deleted_at IS NULL
            ")[0];

            // Stats de paiement
            $paymentStats = DB::select("
                SELECT 
                    COUNT(CASE WHEN paiements.status = 1 THEN 1 END) as paye,
                    COUNT(CASE WHEN paiements.status = 0 THEN 1 END) as non_paye
                FROM paiements
                INNER JOIN prescriptions ON paiements.prescription_id = prescriptions.id
                WHERE prescriptions.deleted_at IS NULL
                AND paiements.deleted_at IS NULL
            ")[0];

            $total = (int) $stats->total;
            $completed = (int) $stats->termine + (int) $stats->valide + (int) $stats->a_refaire;
            $validated_or_to_redo = (int) $stats->valide + (int) $stats->a_refaire;

            return [
                'prescriptions' => (int) $stats->en_attente + (int) $stats->en_cours + (int) $stats->termine,
                'total' => $total,
                'en_attente' => (int) $stats->en_attente,
                'en_cours' => (int) $stats->en_cours,
                'termine' => (int) $stats->termine,
                'validees' => (int) $stats->valide,
                'a_refaire' => (int) $stats->a_refaire,
                'countPaye' => (int) $paymentStats->paye,
                'countNonPaye' => (int) $paymentStats->non_paye,
                'taux_completion' => $total > 0 ? round(($completed / $total) * 100) : 0,
                'taux_validation' => $validated_or_to_redo > 0 ? round(((int) $stats->valide / $validated_or_to_redo) * 100) : 0,
                'taux_paiement' => ($paymentStats->paye + $paymentStats->non_paye) > 0
                    ? round(($paymentStats->paye / ($paymentStats->paye + $paymentStats->non_paye)) * 100, 2)
                    : 0
            ];
        });
    }

    // =====================================
    // 🔍 REQUÊTE DE BASE (CORRIGÉE)
    // =====================================

    private function getBaseQuery()
    {
        // Vérifier les colonnes disponibles dans votre table prescriptions
        $query = Prescription::query()
            ->select([
                'prescriptions.id',
                'prescriptions.reference',
                'prescriptions.status',
                'prescriptions.patient_id',
                'prescriptions.prescripteur_id',
                'prescriptions.created_at',
                'prescriptions.updated_at',
            ])
            ->with([
                'patient:id,nom,prenom,telephone',
                'prescripteur:id,nom',
                'paiements:id,prescription_id,status,montant,date_paiement'
            ])
            ->withCount('analyses')
            ->whereHas('patient', function ($q) {
                $q->whereNull('deleted_at');
            });

        // Recherche
        if ($this->search) {
            $search = '%' . $this->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', $search)
                    ->orWhereHas('patient', function ($q) use ($search) {
                        $q->where('nom', 'like', $search)
                            ->orWhere('prenom', 'like', $search)
                            ->orWhere('telephone', 'like', $search);
                    });
            });
        }

        // Filtre par date
        if ($this->dateFilter) {
            switch ($this->dateFilter) {
                case 'today':
                    $query->whereDate('prescriptions.created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('prescriptions.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('prescriptions.created_at', now()->month)
                        ->whereYear('prescriptions.created_at', now()->year);
                    break;
            }
        }

        // Filtre par dates personnalisées
        if ($this->dateDebut) {
            $query->whereDate('prescriptions.created_at', '>=', $this->dateDebut);
        }
        if ($this->dateFin) {
            $query->whereDate('prescriptions.created_at', '<=', $this->dateFin);
        }

        // Filtre par prescripteur
        if ($this->prescripteurFilter) {
            $query->where('prescripteur_id', $this->prescripteurFilter);
        }

        // Filtre par technicien (AJOUTÉ - était manquant)
        if (!empty($this->technicienFilter)) {
            $query->where('technicien_id', $this->technicienFilter);
        }

        if ($this->paymentStatusFilter === 'paid') {
            $query->whereHas('paiements', function ($q) {
                $q->where('status', 1);
            });
        }
        if ($this->paymentStatusFilter === 'unpaid') {
            $query->whereDoesntHave('paiements', function ($q) {
                $q->where('status', 1);
            });
        }

        return $query->orderBy($this->sortField, $this->sortDirection);
    }

    // =====================================
    // 📋 DONNÉES PAR ONGLET
    // =====================================

    #[Computed]
    public function prescriptions()
    {
        return $this->getBaseQuery()
            ->whereIn('status', ['EN_ATTENTE', 'EN_COURS', 'TERMINE'])
            ->paginate(15, ['*'], 'page-all');
    }

    #[Computed]
    public function prescriptionsEnAttente()
    {
        return $this->getBaseQuery()
            ->where('status', 'EN_ATTENTE')
            ->paginate(15, ['*'], 'page-attente');
    }

    #[Computed]
    public function prescriptionsEnCours()
    {
        return $this->getBaseQuery()
            ->where('status', 'EN_COURS')
            ->paginate(15, ['*'], 'page-cours');
    }

    #[Computed]
    public function prescriptionsTermine()
    {
        return $this->getBaseQuery()
            ->where('status', 'TERMINE')
            ->paginate(15, ['*'], 'page-termine');
    }

    #[Computed]
    public function prescriptionsValidees()
    {
        return $this->getBaseQuery()
            ->where('status', 'VALIDE')
            ->paginate(15, ['*'], 'page-valide');
    }

    #[Computed]
    public function prescriptionsARefaire()
    {
        return $this->getBaseQuery()
            ->where('status', 'A_REFAIRE')
            ->paginate(15, ['*'], 'page-refaire');
    }

    #[Computed]
    public function prescripteurs()
    {
        return Prescripteur::orderBy('nom')->get();
    }

    #[Computed]
    public function techniciens()
    {
        return User::where('type', 'technicien')->orderBy('name')->get();
    }

    // =====================================
    // 🎯 ACTIONS
    // =====================================

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function setDateFilter($filter)
    {
        $this->dateFilter = $filter;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->dateFilter = '';
        $this->prescripteurFilter = '';
        $this->technicienFilter = '';
        $this->dateDebut = '';
        $this->dateFin = '';
        $this->paymentStatusFilter = '';
        $this->resetPage();
    }

    public function toggleAdvancedFilters()
    {
        $this->showAdvancedFilters = !$this->showAdvancedFilters;
    }

    // =====================================
    // 📝 MODALS
    // =====================================

    public function openAssignModal($id)
    {
        $this->selectedPrescriptionId = $id;
        $this->technicienId = null;
        $this->showAssignModal = true;
    }

    public function closeAssignModal()
    {
        $this->showAssignModal = false;
        $this->selectedPrescriptionId = null;
        $this->technicienId = null;
    }

    public function assignTechnician()
    {
        if (!$this->selectedPrescriptionId || !$this->technicienId) {
            session()->flash('error', 'Veuillez sélectionner un technicien.');
            return;
        }

        try {
            $prescription = Prescription::findOrFail($this->selectedPrescriptionId);

            $prescription->update([
                'technicien_id' => $this->technicienId,
                'status' => 'EN_COURS',
            ]);

            Cache::forget('admin_prescription_stats');

            $this->closeAssignModal();
            session()->flash('success', "La prescription a été assignée et son statut est maintenant 'En cours'.");

        } catch (\Exception $e) {
            session()->flash('error', "Erreur lors de l'assignation : " . $e->getMessage());
        }
    }

    public function showDetails($id)
    {
        $this->selectedPrescriptionId = $id;
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedPrescriptionId = null;
    }

    public function openChangeStatusModal($id)
    {
        $this->selectedPrescriptionId = $id;
        $this->newStatus = null;
        $this->commentaire = null;
        $this->showChangeStatusModal = true;
    }

    public function closeChangeStatusModal()
    {
        $this->showChangeStatusModal = false;
        $this->selectedPrescriptionId = null;
        $this->newStatus = null;
        $this->commentaire = null;
    }

    public function changeStatus()
    {
        if (!$this->selectedPrescriptionId || !$this->newStatus) {
            session()->flash('error', 'Veuillez sélectionner un statut.');
            return;
        }

        try {
            $prescription = Prescription::findOrFail($this->selectedPrescriptionId);
            $oldStatus = $prescription->status;

            $prescription->update([
                'status' => $this->newStatus,
            ]);

            // Invalider le cache
            Cache::forget('admin_prescription_stats');

            $this->closeChangeStatusModal();
            session()->flash('success', "Statut modifié de '{$oldStatus}' à '{$this->newStatus}'.");

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors du changement de statut : ' . $e->getMessage());
        }
    }

    #[Computed]
    public function selectedPrescription()
    {
        if (!$this->selectedPrescriptionId) {
            return null;
        }

        return Prescription::with(['patient', 'prescripteur', 'analyses', 'paiements'])
            ->find($this->selectedPrescriptionId);
    }

    // =====================================
    // 💰 GESTION DES PAIEMENTS
    // =====================================

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

            $paiement = Paiement::whereHas(
                'prescription',
                fn($q) =>
                $q->where('id', $this->selectedPrescriptionForPayment)
            )->firstOrFail();

            $paiement->changerStatutPaiement(true);

            session()->flash('success', 'Paiement marqué comme payé.');
            $this->refreshStats();
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

            $paiement = Paiement::whereHas(
                'prescription',
                fn($q) =>
                $q->where('id', $this->selectedPrescriptionForPayment)
            )->firstOrFail();

            $paiement->changerStatutPaiement(false);

            session()->flash('success', 'Paiement marqué comme non payé.');
            $this->refreshStats();
            $this->resetModal();

        } catch (\Exception $e) {
            Log::error('Erreur marquage paiement non payé', ['error' => $e->getMessage()]);
            session()->flash('error', 'Erreur lors du marquage.');
            $this->resetModal();
        }
    }

    public function resetModal()
    {
        $this->showDetailsModal = false;
        $this->showChangeStatusModal = false;
        $this->showAssignModal = false;
        $this->showConfirmPaymentModal = false;
        $this->showConfirmUnpaymentModal = false;
        $this->selectedPrescriptionId = null;
        $this->selectedPrescriptionForPayment = null;
        $this->technicienId = null;
        $this->newStatus = null;
        $this->commentaire = null;
        $this->paymentAction = null;
    }

    // =====================================
    // 📄 MÉTHODES POUR LA FACTURE & PDF
    // =====================================

    public function ouvrirFacture($id = null)
    {
        $id = $id ?? $this->selectedPrescriptionId;
        if (!$id) {
            session()->flash('error', 'Aucune prescription sélectionnée');
            return;
        }

        $url = route('secretaire.prescription.facture', $id);
        $this->dispatch('open-window', ['url' => $url]);
    }

    public function imprimerFacture($id = null)
    {
        $id = $id ?? $this->selectedPrescriptionId;
        if (!$id) {
            session()->flash('error', 'Aucune prescription sélectionnée');
            return;
        }

        $url = route('secretaire.prescription.facture', $id) . '?print=1';
        $this->dispatch('open-window', ['url' => $url]);
    }

    public function telechargerFacturePDF($id = null)
    {
        $this->ouvrirFacture($id);
    }

    public function ouvrirPDF($id = null)
    {
        $id = $id ?? $this->selectedPrescriptionId;
        if (!$id) {
            session()->flash('error', 'Aucune prescription sélectionnée');
            return;
        }

        $url = route('laboratoire.prescription.pdf', $id);
        $this->dispatch('open-window', ['url' => $url]);
    }

    public function telechargerPDF($id = null)
    {
        $this->ouvrirPDF($id);
    }

    // =====================================
    // 🖼️ RENDER
    // =====================================

    public function render()
    {
        $data = [];

        switch ($this->activeTab) {
            case 'en_attente':
                $data['data'] = $this->prescriptionsEnAttente;
                break;
            case 'en_cours':
                $data['data'] = $this->prescriptionsEnCours;
                break;
            case 'termine':
                $data['data'] = $this->prescriptionsTermine;
                break;
            case 'validees':
                $data['data'] = $this->prescriptionsValidees;
                break;
            case 'a_refaire':
                $data['data'] = $this->prescriptionsARefaire;
                break;
            default:
                $data['data'] = $this->prescriptions;
        }

        return view('livewire.admin.gestion-analyses', $data);
    }
}
