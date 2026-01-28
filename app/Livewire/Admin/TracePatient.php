<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Patient;
use App\Models\Prescription;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TracePatient extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $activeTab = 'patients';

    public $selectedPatients = [];
    public $selectedPrescriptions = [];
    public $selectAllPatients = false;
    public $selectAllPrescriptions = false;

    public $confirmingForceDeletePatient = false;
    public $confirmingForceDeletePrescription = false;
    public $patientToDelete;
    public $prescriptionToDelete;

    public $confirmingEmptyPatientsTrash = false;
    public $confirmingEmptyPrescriptionsTrash = false;

    protected $listeners = [
        'refreshTrash' => '$refresh',
        'updateTraceCount' => 'updateCount'
    ];

    public function mount()
    {
        $this->activeTab = 'prescriptions';
    }

    public function updateCount()
    {
        $this->dispatch('updateTraceCount', [
            'patients' => Patient::onlyTrashed()->count(),
            'prescriptions' => Prescription::onlyTrashed()->count()
        ]);
    }

    public function render()
    {
        $patients = Patient::onlyTrashed()
            ->withCount('prescriptions')
            ->when($this->search, function ($query) {
                $query->where('numero_dossier', 'like', '%' . $this->search . '%')
                    ->orWhere('nom', 'like', '%' . $this->search . '%')
                    ->orWhere('prenom', 'like', '%' . $this->search . '%')
                    ->orWhere('telephone', 'like', '%' . $this->search . '%');
            })
            ->orderBy('deleted_at', 'desc')
            ->paginate($this->perPage, ['*'], 'patients_page');

        $prescriptions = Prescription::onlyTrashed()
            ->with(['patient', 'prescripteur'])
            ->when($this->search, function ($query) {
                $query->where('reference', 'like', '%' . $this->search . '%')
                    ->orWhereHas('patient', function ($q) {
                        $q->where('nom', 'like', '%' . $this->search . '%')
                            ->orWhere('prenom', 'like', '%' . $this->search . '%')
                            ->orWhere('numero_dossier', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('prescripteur', function ($q) {
                        $q->where('nom', 'like', '%' . $this->search . '%');
                    });
            })
            ->orderBy('deleted_at', 'desc')
            ->paginate($this->perPage, ['*'], 'prescriptions_page');

        $patientsCount = Patient::onlyTrashed()->count();
        $patientsRecentCount = Patient::onlyTrashed()
            ->where('deleted_at', '>=', now()->subDays(7))
            ->count();
        $patientsOldCount = Patient::onlyTrashed()
            ->where('deleted_at', '<', now()->subDays(30))
            ->count();
        $patientsWithPrescriptionsCount = Patient::onlyTrashed()
            ->has('prescriptions')
            ->count();

        $prescriptionsCount = Prescription::onlyTrashed()->count();
        $prescriptionsRecentCount = Prescription::onlyTrashed()
            ->where('deleted_at', '>=', now()->subDays(7))
            ->count();
        $prescriptionsOldCount = Prescription::onlyTrashed()
            ->where('deleted_at', '<', now()->subDays(30))
            ->count();

        $prescriptionsTotalValue = Prescription::onlyTrashed()
            ->with(['analyses', 'prelevements'])
            ->get()
            ->sum('montant_total');

        $totalCount = $patientsCount + $prescriptionsCount;

        return view('livewire.admin.trace-patient', [
            'patients' => $patients,
            'prescriptions' => $prescriptions,
            'totalCount' => $totalCount,
            'patientsCount' => $patientsCount,
            'patientsRecentCount' => $patientsRecentCount,
            'patientsOldCount' => $patientsOldCount,
            'patientsWithPrescriptionsCount' => $patientsWithPrescriptionsCount,
            'prescriptionsCount' => $prescriptionsCount,
            'prescriptionsRecentCount' => $prescriptionsRecentCount,
            'prescriptionsOldCount' => $prescriptionsOldCount,
            'prescriptionsTotalValue' => $prescriptionsTotalValue,
        ]);
    }

    // ===== MÉTHODES PATIENTS =====

    public function restorePatient($id)
    {
        $patient = Patient::onlyTrashed()->findOrFail($id);
        $patient->restore();

        $this->dispatch('refreshTrash');
        $this->updateCount();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Patient restauré avec succès!'
        ]);
    }

    public function confirmForceDeletePatient($id)
    {
        $this->patientToDelete = Patient::onlyTrashed()
            ->withCount('prescriptions')
            ->findOrFail($id);
        $this->confirmingForceDeletePatient = true;
    }

    /**
     * ✅ CORRECTION : Suppression complète avec toutes les relations
     */
    public function forceDeletePatient()
    {
        if ($this->patientToDelete) {
            try {
                // ✅ Utiliser la méthode de suppression complète
                $this->patientToDelete->forceDeleteWithRelations();

                $this->confirmingForceDeletePatient = false;
                $this->patientToDelete = null;
                $this->dispatch('refreshTrash');
                $this->updateCount();
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Patient et toutes ses données définitivement supprimés!'
                ]);
            } catch (\Exception $e) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Erreur lors de la suppression : ' . $e->getMessage()
                ]);
            }
        }
    }

    /**
     * ✅ CORRECTION : Restauration multiple
     */
    public function restoreSelectedPatients()
    {
        Patient::onlyTrashed()->whereIn('id', $this->selectedPatients)->restore();
        $this->selectedPatients = [];
        $this->selectAllPatients = false;
        $this->dispatch('refreshTrash');
        $this->updateCount();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Patients sélectionnés restaurés!'
        ]);
    }

    /**
     * ✅ CORRECTION : Suppression multiple complète
     */
    public function deleteSelectedPatients()
    {
        try {
            $patients = Patient::onlyTrashed()->whereIn('id', $this->selectedPatients)->get();
            
            foreach ($patients as $patient) {
                $patient->forceDeleteWithRelations();
            }

            $this->selectedPatients = [];
            $this->selectAllPatients = false;
            $this->dispatch('refreshTrash');
            $this->updateCount();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Patients sélectionnés et toutes leurs données définitivement supprimés!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur lors de la suppression : ' . $e->getMessage()
            ]);
        }
    }

    public function confirmEmptyPatientsTrash()
    {
        $this->confirmingEmptyPatientsTrash = true;
    }

    /**
     * ✅ CORRECTION : Vidage complet de la corbeille
     */
    public function emptyPatientsTrash()
    {
        try {
            $patients = Patient::onlyTrashed()->get();
            
            foreach ($patients as $patient) {
                $patient->forceDeleteWithRelations();
            }

            $this->confirmingEmptyPatientsTrash = false;
            $this->selectedPatients = [];
            $this->selectAllPatients = false;
            $this->dispatch('refreshTrash');
            $this->updateCount();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Corbeille des patients vidée avec succès!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur lors du vidage : ' . $e->getMessage()
            ]);
        }
    }

    // ===== MÉTHODES PRESCRIPTIONS =====

    public function restorePrescription($id)
    {
        $prescription = Prescription::onlyTrashed()->findOrFail($id);
        $prescription->restore();

        $this->dispatch('refreshTrash');
        $this->updateCount();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Prescription restaurée avec succès!'
        ]);
    }

    public function confirmForceDeletePrescription($id)
    {
        $this->prescriptionToDelete = Prescription::onlyTrashed()
            ->with(['patient', 'prescripteur'])
            ->findOrFail($id);
        $this->confirmingForceDeletePrescription = true;
    }

    /**
     * ✅ CORRECTION : Suppression complète avec toutes les relations
     */
    public function forceDeletePrescription()
    {
        if ($this->prescriptionToDelete) {
            try {
                // ✅ Utiliser la méthode de suppression complète
                $this->prescriptionToDelete->forceDeleteWithRelations();

                $this->confirmingForceDeletePrescription = false;
                $this->prescriptionToDelete = null;
                $this->dispatch('refreshTrash');
                $this->updateCount();
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Prescription et toutes ses données définitivement supprimées!'
                ]);
            } catch (\Exception $e) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Erreur lors de la suppression : ' . $e->getMessage()
                ]);
            }
        }
    }

    /**
     * ✅ CORRECTION : Restauration multiple
     */
    public function restoreSelectedPrescriptions()
    {
        Prescription::onlyTrashed()->whereIn('id', $this->selectedPrescriptions)->restore();
        $this->selectedPrescriptions = [];
        $this->selectAllPrescriptions = false;
        $this->dispatch('refreshTrash');
        $this->updateCount();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Prescriptions sélectionnées restaurées!'
        ]);
    }

    /**
     * ✅ CORRECTION : Suppression multiple complète
     */
    public function deleteSelectedPrescriptions()
    {
        try {
            $prescriptions = Prescription::onlyTrashed()->whereIn('id', $this->selectedPrescriptions)->get();
            
            foreach ($prescriptions as $prescription) {
                $prescription->forceDeleteWithRelations();
            }

            $this->selectedPrescriptions = [];
            $this->selectAllPrescriptions = false;
            $this->dispatch('refreshTrash');
            $this->updateCount();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Prescriptions sélectionnées et toutes leurs données définitivement supprimées!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur lors de la suppression : ' . $e->getMessage()
            ]);
        }
    }

    public function confirmEmptyPrescriptionsTrash()
    {
        $this->confirmingEmptyPrescriptionsTrash = true;
    }

    /**
     * ✅ CORRECTION : Vidage complet de la corbeille
     */
    public function emptyPrescriptionsTrash()
    {
        try {
            $prescriptions = Prescription::onlyTrashed()->get();
            
            foreach ($prescriptions as $prescription) {
                $prescription->forceDeleteWithRelations();
            }

            $this->confirmingEmptyPrescriptionsTrash = false;
            $this->selectedPrescriptions = [];
            $this->selectAllPrescriptions = false;
            $this->dispatch('refreshTrash');
            $this->updateCount();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Corbeille des prescriptions vidée avec succès!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur lors du vidage : ' . $e->getMessage()
            ]);
        }
    }

    // ===== MÉTHODES DE SÉLECTION (inchangées) =====

    public function updatedSelectAllPatients($value)
    {
        if ($value) {
            $this->selectedPatients = Patient::onlyTrashed()
                ->when($this->search, function ($query) {
                    $query->where('numero_dossier', 'like', '%' . $this->search . '%')
                        ->orWhere('nom', 'like', '%' . $this->search . '%')
                        ->orWhere('prenom', 'like', '%' . $this->search . '%')
                        ->orWhere('telephone', 'like', '%' . $this->search . '%');
                })
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedPatients = [];
        }
    }

    public function updatedSelectAllPrescriptions($value)
    {
        if ($value) {
            $this->selectedPrescriptions = Prescription::onlyTrashed()
                ->when($this->search, function ($query) {
                    $query->where('reference', 'like', '%' . $this->search . '%')
                        ->orWhereHas('patient', function ($q) {
                            $q->where('nom', 'like', '%' . $this->search . '%')
                                ->orWhere('prenom', 'like', '%' . $this->search . '%')
                                ->orWhere('numero_dossier', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('prescripteur', function ($q) {
                            $q->where('nom', 'like', '%' . $this->search . '%');
                        });
                })
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedPrescriptions = [];
        }
    }

    // ===== MÉTHODES UTILITAIRES (inchangées) =====

    public function updatedActiveTab()
    {
        $this->search = '';
        $this->selectedPatients = [];
        $this->selectedPrescriptions = [];
        $this->selectAllPatients = false;
        $this->selectAllPrescriptions = false;
        $this->resetPage('patients_page');
        $this->resetPage('prescriptions_page');
    }

    public function updatedSearch()
    {
        $this->selectedPatients = [];
        $this->selectedPrescriptions = [];
        $this->selectAllPatients = false;
        $this->selectAllPrescriptions = false;
        $this->resetPage('patients_page');
        $this->resetPage('prescriptions_page');
    }
}