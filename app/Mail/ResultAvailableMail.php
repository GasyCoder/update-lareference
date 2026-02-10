<?php

namespace App\Mail;

use App\Models\Prescription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResultAvailableMail extends Mailable
{
    use Queueable, SerializesModels;

    public $prescription;
    public $customMessage;

    public function __construct(Prescription $prescription, string $customMessage)
    {
        $this->prescription = $prescription;
        $this->customMessage = $customMessage;
    }

    public function build()
    {
        return $this->subject('Vos résultats d\'analyses sont disponibles - Laboratoire La Référence')
            ->view('emails.resultats-disponibles');
    }
}
