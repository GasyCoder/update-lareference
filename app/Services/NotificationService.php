<?php

namespace App\Services;

use App\Models\Prescription;
use App\Mail\ResultAvailableMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Envoyer une notification (E-mail ou SMS)
     */
    public function send(Prescription $prescription, string $type, string $message)
    {
        $success = false;

        if ($type === 'email' || $type === 'both') {
            $success = $this->sendEmail($prescription, $message);
        }

        if ($type === 'sms' || $type === 'both') {
            $success = $this->sendSms($prescription, $message) || $success;
        }

        if ($success) {
            $prescription->update(['notified_at' => now()]);
        }

        return $success;
    }

    /**
     * Simulation d'envoi de SMS
     */
    public function sendSms(Prescription $prescription, string $message)
    {
        $phone = $prescription->patient->telephone;

        if (!$phone) {
            Log::warning("Échec envoi SMS : Aucun numéro pour la prescription {$prescription->reference}");
            return false;
        }

        // TODO: Intégrer une API SMS réelle ici (Twilio, etc.)
        Log::info("SIMULATION SMS envoyé à {$phone} : {$message}");

        return true;
    }

    /**
     * Envoi d'E-mail via Laravel Mail
     */
    public function sendEmail(Prescription $prescription, string $message)
    {
        $email = $prescription->patient->email;

        if (!$email) {
            Log::warning("Échec envoi Email : Aucune adresse pour la prescription {$prescription->reference}");
            return false;
        }

        try {
            Mail::to($email)->send(new ResultAvailableMail($prescription, $message));
            Log::info("Email envoyé à {$email} pour la prescription {$prescription->reference}");
            return true;
        } catch (\Exception $e) {
            Log::error("Erreur envoi Email : " . $e->getMessage());
            return false;
        }
    }
}
