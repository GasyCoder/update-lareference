<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'numero_dossier',
        'nom', 
        'prenom', 
        'civilite',
        'date_naissance',
        'telephone', 
        'email',
        'adresse',
        'statut'
    ];

    protected $casts = [
        'date_naissance' => 'date', // ✅ Cast automatique en Carbon
    ];

    // ✅ CONSTANTES POUR LES CIVILITÉS
    const CIVILITES = [
        'Madame',
        'Monsieur', 
        'Mademoiselle',
        'Enfant-garçon',
        'Enfant-fille'
    ];

    // Relations
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function dernierePrescription()
    {
        return $this->hasOne(Prescription::class)->latest();
    }

    public function premierePrescription()
    {
        return $this->hasOne(Prescription::class)->oldest();
    }

    // =====================================
    // ✅ GESTION DATE DE NAISSANCE ET ÂGE
    // =====================================

    /**
     * Calculer l'âge exact en années depuis la date de naissance
     */
    public function getAgeEnAnneesAttribute(): ?int
    {
        if (!$this->date_naissance) {
            return null;
        }

        return Carbon::parse($this->date_naissance)->age;
    }

    /**
     * Calculer l'âge en mois (pour les bébés < 2 ans)
     */
    public function getAgeEnMoisAttribute(): ?int
    {
        if (!$this->date_naissance) {
            return null;
        }

        return Carbon::parse($this->date_naissance)->diffInMonths(now());
    }

    /**
     * Calculer l'âge en jours (pour les nouveau-nés < 1 mois)
     */
    public function getAgeEnJoursAttribute(): ?int
    {
        if (!$this->date_naissance) {
            return null;
        }

        return Carbon::parse($this->date_naissance)->diffInDays(now());
    }

    /**
     * Retourner l'âge avec l'unité la plus appropriée
     * Retourne un tableau ['age' => X, 'unite' => 'Ans|Mois|Jours']
     */
    public function getAgeAvecUniteAttribute(): array
    {
        if (!$this->date_naissance) {
            // Fallback sur la dernière prescription si pas de date de naissance
            $derniere = $this->dernierePrescription;
            return [
                'age' => $derniere?->age ?? 0,
                'unite' => $derniere?->unite_age ?? 'Ans'
            ];
        }

        $jours = $this->age_en_jours;
        $mois = $this->age_en_mois;
        $annees = $this->age_en_annees;

        // Logique pour déterminer l'unité appropriée
        if ($jours <= 60) { // Moins de 2 mois
            return ['age' => $jours, 'unite' => 'Jours'];
        } elseif ($mois < 24) { // Moins de 2 ans
            return ['age' => $mois, 'unite' => 'Mois'];
        } else {
            return ['age' => $annees, 'unite' => 'Ans'];
        }
    }

    /**
     * Formater la date de naissance pour affichage
     */
    public function getDateNaissanceFormateeAttribute(): ?string
    {
        if (!$this->date_naissance) {
            return null;
        }

        return Carbon::parse($this->date_naissance)->locale('fr')->isoFormat('D MMMM YYYY');
    }

    /**
     * Vérifier si la date de naissance est cohérente avec la civilité
     */
    public function isDateNaissanceCoherenteAvecCivilite(): bool
    {
        if (!$this->date_naissance || !$this->civilite) {
            return true; // Pas de conflit si données manquantes
        }

        $age = $this->age_en_annees;
        $isEnfant = in_array($this->civilite, ['Enfant-garçon', 'Enfant-fille']);

        // Un enfant devrait avoir moins de 18 ans
        if ($isEnfant && $age >= 18) {
            return false;
        }

        // Un adulte devrait avoir 18 ans ou plus
        if (!$isEnfant && $age < 18) {
            return false;
        }

        return true;
    }

    // =====================================
    // ✅ GESTION ADRESSE
    // =====================================

    /**
     * Obtenir l'adresse complète formatée
     */
    public function getAdresseCompleteAttribute(): ?string
    {
        $parts = array_filter($this->adresse);
        return !empty($parts) ? implode(', ', $parts) : null;
    }

    /**
     * Vérifier si le patient a une adresse complète
     */
    public function hasAdresseComplete(): bool
    {
        return !empty($this->adresse);
    }

    // =====================================
    // SCOPES EXISTANTS
    // =====================================

    public function scopeFideles($query)
    {
        return $query->where('statut', 'FIDELE');
    }

    public function scopeVip($query)
    {
        return $query->where('statut', 'VIP');
    }

    public function scopeNouveaux($query)
    {
        return $query->where('statut', 'NOUVEAU');
    }

    public function scopeEnfants($query)
    {
        return $query->whereIn('civilite', ['Enfant-garçon', 'Enfant-fille']);
    }

    public function scopeAdultes($query)
    {
        return $query->whereIn('civilite', ['Madame', 'Monsieur', 'Mademoiselle']);
    }

    // ✅ NOUVEAU SCOPE: Patients par tranche d'âge
    public function scopeParTrancheAge($query, $min, $max)
    {
        return $query->whereNotNull('date_naissance')
                    ->whereRaw('TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) BETWEEN ? AND ?', [$min, $max]);
    }

    public function getIsEnfantAttribute()
    {
        return in_array($this->civilite, ['Enfant-garçon', 'Enfant-fille']);
    }

    public function getGenreAttribute()
    {
        switch ($this->civilite) {
            case 'Madame':
            case 'Mademoiselle':
            case 'Enfant-fille':
                return 'F';
            case 'Monsieur':
            case 'Enfant-garçon':
                return 'M';
            default:
                return null;
        }
    }

    // ✅ ACCESSEUR POUR L'ÂGE MIS À JOUR (priorité date_naissance)
    public function getLatestAgeAttribute()
    {
        if ($this->date_naissance) {
            return $this->age_avec_unite['age'];
        }
        
        $latestPrescription = $this->dernierePrescription;
        return $latestPrescription ? $latestPrescription->age : null;
    }

    public function getLatestUniteAgeAttribute()
    {
        if ($this->date_naissance) {
            return $this->age_avec_unite['unite'];
        }
        
        $latestPrescription = $this->dernierePrescription;
        return $latestPrescription ? $latestPrescription->unite_age : 'Ans';
    }

    public function getNomCompletAttribute()
    {
        return trim(($this->prenom ? $this->prenom . ' ' : '') . $this->nom);
    }

    // =====================================
    // MÉTHODES STATISTIQUES
    // =====================================

    public function getTotalPrescriptionsAttribute()
    {
        return $this->prescriptions_count ?? $this->prescriptions()->count();
    }

    public function getTotalAnalysesAttribute()
    {
        return $this->prescriptions()
                   ->withCount('analyses')
                   ->get()
                   ->sum('analyses_count');
    }

    public function getTotalPaiementsAttribute()
    {
        return $this->prescriptions()
                   ->withCount('paiements')
                   ->get()
                   ->sum('paiements_count');
    }

    public function getMontantTotalPayeAttribute()
    {
        return $this->prescriptions()
                   ->with('paiements')
                   ->get()
                   ->flatMap->paiements
                   ->sum('montant');
    }

    public function getStatutAutomatiqueAttribute()
    {
        $nombrePrescriptions = $this->getTotalPrescriptionsAttribute();
        $montantTotal = $this->getMontantTotalPayeAttribute();
        
        if ($montantTotal >= 500000 || $nombrePrescriptions >= 10) {
            return 'VIP';
        } elseif ($nombrePrescriptions >= 3) {
            return 'FIDELE';
        }
        
        return 'NOUVEAU';
    }

    public function getDerniereVisiteAttribute()
    {
        $dernierePrescription = $this->dernierePrescription;
        return $dernierePrescription ? $dernierePrescription->created_at : null;
    }

    // =====================================
    // BOOT ET GÉNÉRATION NUMÉRO DOSSIER
    // =====================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($patient) {
            if (empty($patient->numero_dossier)) {
                $patient->numero_dossier = static::genererNumeroDossier();
            }
            
            if (empty($patient->statut)) {
                $patient->statut = 'NOUVEAU';
            }

            // ✅ Suggestion automatique de civilité basée sur l'âge
            if ($patient->date_naissance && empty($patient->civilite)) {
                $age = Carbon::parse($patient->date_naissance)->age;
                if ($age < 18) {
                    // Laisser vide pour que l'utilisateur choisisse le genre
                    Log::info('Patient mineur détecté, civilité à définir', ['age' => $age]);
                }
            }
        });

        static::saving(function ($patient) {
            if ($patient->statut === 'NOUVEAU' && $patient->exists) {
                $patient->statut = $patient->getStatutAutomatiqueAttribute();
            }

            // ✅ Validation cohérence âge-civilité
            if ($patient->date_naissance && $patient->civilite) {
                if (!$patient->isDateNaissanceCoherenteAvecCivilite()) {
                    Log::warning('Incohérence âge-civilité détectée', [
                        'patient_id' => $patient->id,
                        'age' => $patient->age_en_annees,
                        'civilite' => $patient->civilite
                    ]);
                }
            }
        });
    }

    public static function genererNumeroDossier()
    {
        $annee = date('Y');
        
        $compteur = static::withTrashed()
                         ->whereRaw('YEAR(created_at) = ?', [$annee])
                         ->count() + 1;
        
        $numero = str_pad($compteur, 5, '0', STR_PAD_LEFT);
        $dossier = "DOS-{$annee}-{$numero}";
        
        while (static::withTrashed()->where('numero_dossier', $dossier)->exists()) {
            $compteur++;
            $numero = str_pad($compteur, 5, '0', STR_PAD_LEFT);
            $dossier = "DOS-{$annee}-{$numero}";
        }
        
        return $dossier;
    }

    public static function getNextNumeroDossier()
    {
        $annee = date('Y');
        $compteur = static::withTrashed()
                         ->whereRaw('YEAR(created_at) = ?', [$annee])
                         ->count() + 1;
        
        $numero = str_pad($compteur, 5, '0', STR_PAD_LEFT);
        return "DOS-{$annee}-{$numero}";
    }

    // =====================================
    // SCOPES DE RECHERCHE
    // =====================================

    public function scopeRechercher($query, $terme)
    {
        return $query->where(function ($q) use ($terme) {
            $q->where('numero_dossier', 'like', "%{$terme}%")
              ->orWhere('nom', 'like', "%{$terme}%")
              ->orWhere('prenom', 'like', "%{$terme}%")
              ->orWhere('telephone', 'like', "%{$terme}%")
              ->orWhere('email', 'like', "%{$terme}%")
              ->orWhere('adresse', 'like', "%{$terme}%"); // ✅ NOUVEAU
        });
    }

    public function scopeActifs($query, $jours = 30)
    {
        return $query->whereHas('prescriptions', function ($q) use ($jours) {
            $q->where('created_at', '>=', now()->subDays($jours));
        });
    }

    public function archiver()
    {
        $prescriptionsEnCours = $this->prescriptions()
            ->whereIn('status', [
                Prescription::STATUS_EN_ATTENTE,
                Prescription::STATUS_EN_COURS
            ])->count();
        
        if ($prescriptionsEnCours > 0) {
            throw new \Exception('Impossible d\'archiver ce patient car il a des prescriptions en cours.');
        }
        
        return $this->delete();
    }



    /**
     * Supprimer définitivement le patient avec toutes ses relations
     */
    public function forceDeleteWithRelations()
    {
        DB::beginTransaction();
        
        try {
            // 1. Récupérer tous les IDs de prescriptions (actives + soft deleted)
            $prescriptionIds = DB::table('prescriptions')
                ->where('patient_id', $this->id)
                ->pluck('id');
            
            if ($prescriptionIds->isNotEmpty()) {
                // 2. Supprimer toutes les dépendances des prescriptions
                DB::table('antibiogrammes')
                    ->whereIn('prescription_id', $prescriptionIds)
                    ->delete();
                
                DB::table('resultats')
                    ->whereIn('prescription_id', $prescriptionIds)
                    ->delete();
                
                DB::table('tubes')
                    ->whereIn('prescription_id', $prescriptionIds)
                    ->delete();
                
                DB::table('paiements')
                    ->whereIn('prescription_id', $prescriptionIds)
                    ->delete();
                
                DB::table('prescription_analyse')
                    ->whereIn('prescription_id', $prescriptionIds)
                    ->delete();
                
                // 3. Supprimer les prescriptions elles-mêmes
                DB::table('prescriptions')
                    ->whereIn('id', $prescriptionIds)
                    ->delete();
            }
            
            // 4. Supprimer le patient lui-même
            $this->forceDelete();
            
            DB::commit();
            
            \Log::info('Patient supprimé définitivement avec toutes ses relations', [
                'patient_id' => $this->id,
                'numero_dossier' => $this->numero_dossier,
                'prescriptions_count' => $prescriptionIds->count()
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollback();
            
            \Log::error('Erreur lors de la suppression définitive du patient', [
                'patient_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
}