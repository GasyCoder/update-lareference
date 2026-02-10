<?php

namespace App\Livewire\Secretaire\Prescription;

use Livewire\Component;
use App\Models\Prescription;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class NotificationModal extends Component
{
    public $show = false;
    public $prescriptionId;
    public $prescription;
    public $message;
    public $type = 'both'; // 'sms', 'email', 'both'
    public $hasSms = false;
    public $hasEmail = false;

    protected $listeners = ['openNotificationModal' => 'open'];

    public function open($prescriptionId)
    {
        $this->prescriptionId = $prescriptionId;
        $this->prescription = Prescription::with('patient')->findOrFail($prescriptionId);

        $this->hasSms = !empty($this->prescription->patient->telephone);
        $this->hasEmail = !empty($this->prescription->patient->email);

        // Déterminer le type par défaut
        if ($this->hasSms && $this->hasEmail) {
            $this->type = 'both';
        } elseif ($this->hasSms) {
            $this->type = 'sms';
        } elseif ($this->hasEmail) {
            $this->type = 'email';
        }

        $this->message = "Bonjour,\nNous vous informons que vos résultats d’analyses sont désormais disponibles auprès de notre laboratoire.\nRéférence : {$this->prescription->reference}\nNous vous remercions de votre confiance.";

        $this->show = true;
    }

    public function close()
    {
        $this->show = false;
        $this->reset(['prescriptionId', 'prescription', 'message', 'type', 'hasSms', 'hasEmail']);
    }

    public function send(NotificationService $notificationService)
    {
        if (!$this->prescriptionId)
            return;

        try {
            $success = $notificationService->send($this->prescription, $this->type, $this->message);

            if ($success) {
                $this->dispatch('notificationSent');
                session()->flash('success', 'Notification envoyée avec succès.');
            } else {
                session()->flash('error', 'Erreur lors de l\'envois de la notification.');
            }
        } catch (\Exception $e) {
            Log::error("Erreur Notification : " . $e->getMessage());
            session()->flash('error', 'Une erreur est survenue.');
        }

        $this->close();
    }

    public function render()
    {
        return view('livewire.secretaire.prescription.modals.notification-modal');
    }
}
