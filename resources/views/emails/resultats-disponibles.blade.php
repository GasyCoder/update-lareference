@component('mail::message')
# Bonjour {{ $prescription->patient->civilite }} {{ $prescription->patient->nom }},

{{ $customMessage }}

**Référence :** {{ $prescription->reference }}

Merci de votre confiance.

Cordialement,<br>
L'équipe du **Laboratoire La Référence**
@endcomponent