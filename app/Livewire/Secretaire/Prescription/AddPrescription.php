<?php

namespace App\Livewire\Secretaire\Prescription;

use App\Models\Tube;
use App\Models\Analyse;
use App\Models\Patient;
use App\Models\Setting;
use Livewire\Component;
use App\Models\Paiement;
use App\Models\Prelevement;
use App\Models\Prescripteur;
use App\Models\Prescription;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AddPrescription extends Component
{
    use WithPagination;

    private const SESSION_KEY = 'prescription_en_cours';

    #[Url(as: 'step', except: 'patient', history: true)]
    public string $etape = 'patient';

    public bool $isEditMode = false;
    public bool $activer_remise = false;
    public bool $afficherFactureComplete = false;

    public ?Prescription $prescription = null;
    
    // 👤 DONNÉES PATIENT
    public ?Patient $patient = null;
    public bool $nouveauPatient = false;
    public string $recherchePatient = '';

    // Données nouveau patient
    public string $nom = '';
    public string $prenom = '';
    public string $civilite = 'Monsieur';
    public ?string $dateNaissance = null;
    public string $telephone = '';
    public string $email = '';
    public string $adresse = '';
    
    // 📋 INFORMATIONS CLINIQUES
    public ?int $prescripteurId = null;
    public string $patientType = 'EXTERNE';
    public int $age = 0;
    public string $uniteAge = 'Ans';
    public ?float $poids = null;
    public ?string $renseignementClinique = null;
    
    // 🧪 ANALYSES SÉLECTIONNÉES
    public array $analysesPanier = [];
    public string $rechercheAnalyse = '';
    public ?int $categorieOuverte = null;
    public $parentRecherche = null;
    
    // 🧾 PRÉLÈVEMENTS SÉLECTIONNÉES
    public array $prelevementsSelectionnes = [];
    public string $recherchePrelevement = '';
    
    // 💰 PAIEMENT
    public string $modePaiement = 'ESPECES';
    public float $montantPaye = 0;
    public float $remise = 0;
    public float $total = 0;
    public float $monnaieRendue = 0;
    public $reference;

    public bool $paiementStatut = true;
    
    // 🧪 TUBES
    public array $tubesGeneres = [];

    // =====================================
    // 💾 GESTION DE LA PERSISTANCE SESSION
    // =====================================

    protected function getPersistableProperties(): array
    {
        return [
            'etape', 'nouveauPatient', 'nom', 'prenom', 'civilite', 'dateNaissance',
            'telephone', 'email', 'adresse',
            'prescripteurId', 'patientType', 'age', 'uniteAge', 'poids', 'renseignementClinique',
            'analysesPanier', 'prelevementsSelectionnes', 'modePaiement', 'montantPaye', 
            'remise', 'total', 'monnaieRendue', 'reference', 'tubesGeneres', 'paiementStatut'
        ];  
    }

    protected function sauvegarderSession(): void
    {
        try {
            $data = [];
            foreach ($this->getPersistableProperties() as $property) {
                $data[$property] = $this->$property;
            }
            
            if ($this->patient) {
                $data['patient_id'] = $this->patient->id;
            }

            session()->put(self::SESSION_KEY, $data);
        } catch (\Exception $e) {
            Log::error('Erreur sauvegarde session prescription', ['error' => $e->getMessage()]);
        }
    }

    protected function chargerSession(): void
    {
        try {
            $data = session()->get(self::SESSION_KEY);
            
            if (!$data || !is_array($data)) {
                return;
            }

            foreach ($this->getPersistableProperties() as $property) {
                if (isset($data[$property])) {
                    $this->$property = $data[$property];
                }
            }

            if (!empty($data['patient_id'])) {
                $this->patient = Patient::find($data['patient_id']);
                if ($this->patient) {
                    $this->synchroniserDonneesPatient();
                }
            }

            $this->calculerTotaux();

        } catch (\Exception $e) {
            Log::error('Erreur chargement session prescription', ['error' => $e->getMessage()]);
        }
    }

    protected function synchroniserDonneesPatient(): void
    {
        if (!$this->patient) {
            return;
        }

        $this->nom = $this->patient->nom;
        $this->prenom = $this->patient->prenom ?? '';
        $this->civilite = $this->patient->civilite;
        $this->telephone = $this->patient->telephone ?? '';
        $this->email = $this->patient->email ?? '';
        
        $this->dateNaissance = $this->patient->date_naissance 
            ? $this->patient->date_naissance->format('Y-m-d') 
            : null;
        
        $this->adresse = $this->patient->adresse ?? '';
        
        if ($this->patient->date_naissance) {
            $ageData = $this->patient->age_avec_unite;
            $this->age = $ageData['age'];
            $this->uniteAge = $ageData['unite'];
        }
    }

    protected function viderSession(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    // =====================================
    // 🎯 HOOKS LIVEWIRE POUR AUTO-SAVE
    // =====================================

    public function updated($property, $value): void
    {
        $autoSaveProperties = [
            'nom', 'prenom', 'civilite', 'dateNaissance', 'telephone', 'email',
            'adresse',
            'prescripteurId', 'patientType', 'age', 'uniteAge', 'poids', 'renseignementClinique',
            'modePaiement', 'montantPaye', 'remise', 'paiementStatut'
        ];

        if (in_array($property, $autoSaveProperties)) {
            $this->sauvegarderSession();
        }
    }

    public function updatedDateNaissance($value): void
    {
        if (!empty($value)) {
            try {
                $dateNaissance = \Carbon\Carbon::parse($value);
                $now = \Carbon\Carbon::now();
                
                $jours = $dateNaissance->diffInDays($now);
                $mois = $dateNaissance->diffInMonths($now);
                $annees = $dateNaissance->age;
                
                if ($jours <= 60) {
                    $this->age = $jours;
                    $this->uniteAge = 'Jours';
                } elseif ($mois < 24) {
                    $this->age = $mois;
                    $this->uniteAge = 'Mois';
                } else {
                    $this->age = $annees;
                    $this->uniteAge = 'Ans';
                }
                
                if ($annees < 18 && !in_array($this->civilite, ['Enfant-garçon', 'Enfant-fille'])) {
                    flash()->info('Patient mineur détecté - Pensez à sélectionner "Garçon" ou "Fille"');
                }
                
                $this->sauvegarderSession();
                
            } catch (\Exception $e) {
                Log::error('Erreur calcul âge depuis date naissance', ['error' => $e->getMessage()]);
            }
        }
    }

    public function updatedAnalysesPanier(): void
    {
        $this->calculerTotaux();
        $this->sauvegarderSession();
    }

    public function updatedPrelevementsSelectionnes(): void
    {
        $this->calculerTotaux();
        $this->sauvegarderSession();
    }

    public function updatedRemise(): void
    {
        $this->remise = max(0, $this->remise);
        $this->calculerTotaux();
        $this->sauvegarderSession();
    }

    public function updatedMontantPaye(): void
    {
        $this->montantPaye = max(0, $this->montantPaye);
        $this->calculerMonnaie();
        $this->sauvegarderSession();
    }

    // =====================================
    // 🚀 MOUNT ET INITIALISATION
    // =====================================

    public function mount()
    {
        $this->chargerSession();

        if (empty($this->civilite) || !in_array($this->civilite, Patient::CIVILITES)) {
            $this->civilite = 'Monsieur';
        }
        
        $this->validateEtape();
        $this->chargerSettingsRemise();
        
        if (empty($this->reference)) {
            $this->reference = (new Prescription())->genererReferenceUnique();
        }

        if (empty($this->modePaiement)) {
            $premiereMethode = PaymentMethod::where('is_active', true)
                                ->orderBy('display_order')
                                ->first();
            $this->modePaiement = $premiereMethode?->code ?? 'ESPECES';
        }

        $this->isEditMode = false;
        $this->calculerTotaux();
        $this->sauvegarderSession();
    }

    // =====================================
    // 📊 MÉTHODES POUR LA FACTURE
    // =====================================
    
    public function afficherFactureComplete()
    {
        $this->afficherFactureComplete = true;
    }

    public function fermerFacture()
    {
        $this->afficherFactureComplete = false;
    }

    public function facture()
    {
        if (!$this->prescription) {
            if ($this->reference) {
                $this->prescription = Prescription::where('reference', $this->reference)->first();
            }
            
            if (!$this->prescription) {
                return redirect()->back()->with('error', 'Aucune prescription à facturer');
            }
        }
        
        return view('livewire.secretaire.prescription.facture-impression', [
            'prescription' => $this->prescription
        ]);
    }

    public function getTitle()
    {
        if ($this->prescription) {
            return 'Référence: ' . $this->prescription->reference;
        } elseif ($this->reference) {
            return 'Référence: ' . $this->reference;
        } else {
            return 'Nouvelle prescription';
        }
    }

    // =====================================
    // 📊 PROPRIÉTÉS CALCULÉES
    // =====================================
    
    public function getMethodesPaiementProperty()
    {
        return PaymentMethod::where('is_active', true)
                        ->orderBy('display_order')
                        ->get();
    }

    private function chargerSettingsRemise()
    {
        $setting = Setting::first();
        $this->activer_remise = $setting?->activer_remise ?? false;
    }

    // =====================================
    // 🌐 GESTION URL ET NAVIGATION
    // =====================================
    
    private function validateEtape()
    {
        $etapesValides = ['patient', 'clinique', 'analyses', 'prelevements', 'paiement', 'tubes', 'confirmation'];
        
        if (!in_array($this->etape, $etapesValides)) {
            $this->etape = 'patient';
        }
    }
    
    public function allerEtape(string $etape)
    {
        if (!$this->etapeAccessible($etape)) {
            flash()->warning('Veuillez compléter les étapes précédentes');
            return;
        }
        
        $this->etape = $etape;
        $this->sauvegarderSession();
        
        flash()->info('Navigation vers étape: ' . ucfirst($etape));
    }
    
    private function etapeAccessible(string $etape): bool
    {
        switch ($etape) {
            case 'patient':
                return true;
            case 'clinique':
                return $this->patient !== null;
            case 'analyses':
                return $this->patient !== null && $this->prescripteurId !== null;
            case 'prelevements':
                return !empty($this->analysesPanier);
            case 'paiement':
                return !empty($this->analysesPanier);
            case 'tubes':
                return $this->total > 0 && !empty($this->prelevementsSelectionnes);
            case 'confirmation':
                return (!empty($this->tubesGeneres) || empty($this->prelevementsSelectionnes)) || $this->etape === 'confirmation';
            default:
                return false;
        }
    }

    // =====================================
    // 👤 ÉTAPE 1: GESTION PATIENT
    // =====================================
    
    public function selectionnerPatient(int $patientId)
    {
        $this->patient = Patient::find($patientId);
        $this->nouveauPatient = false;
        
        // Pré-remplir avec dernières données connues
        $dernierePrescription = Prescription::where('patient_id', $patientId)->latest()->first();
        if ($dernierePrescription) {
            // ✅ Donner priorité à la date de naissance si présente
            if (!$this->patient->date_naissance) {
                $this->age = $dernierePrescription->age ?? 0;
                $this->uniteAge = $dernierePrescription->unite_age ?? 'Ans';
            }
            $this->poids = $dernierePrescription->poids;
            $this->prescripteurId = $dernierePrescription->prescripteur_id;
        }

        $this->synchroniserDonneesPatient();
        $this->nouveauPatient = true;
        $this->etape = 'patient';
        $this->sauvegarderSession();
        
        flash()->success("Patient « {$this->patient->nom} {$this->patient->prenom} » sélectionné - Vous pouvez modifier ses informations");
    }
    
    public function creerNouveauPatient()
    {
        $this->nouveauPatient = true;
        $this->patient = null;
        $this->etape = 'patient';
        
        $this->nom = '';
        $this->prenom = '';
        $this->civilite = 'Monsieur';
        $this->dateNaissance = null;
        $this->telephone = '';
        $this->email = '';
        $this->adresse = '';
        
        $this->sauvegarderSession();
        
        flash()->info('Nouveau Patient : Remplissez les informations ci-dessous');
    }

    public function nouveauPrescription()
    {
        $this->viderSession();
        
        $this->reset([
            'patient', 'nouveauPatient', 'nom', 'prenom', 'civilite', 'dateNaissance', 'telephone', 'email',
            'adresse',
            'prescripteurId', 'age', 'poids', 'renseignementClinique',
            'analysesPanier', 'prelevementsSelectionnes', 'tubesGeneres',
            'montantPaye', 'remise', 'total', 'monnaieRendue', 'recherchePatient', 
            'rechercheAnalyse', 'recherchePrelevement', 'afficherFactureComplete', 'prescription'
        ]);
        
        $this->etape = 'patient';
        $this->age = 0;
        $this->uniteAge = 'Ans';
        $this->patientType = 'EXTERNE';
        $this->modePaiement = 'ESPECES';
        $this->civilite = 'Monsieur';
        
        $this->chargerSettingsRemise();
        $this->reference = (new Prescription())->genererReferenceUnique();
        $this->calculerTotaux();
        $this->sauvegarderSession();

        flash()->info('Nouvelle prescription initialisée');
    }
    
    public function validerNouveauPatient()
    {
        $this->validate([
            'nom' => 'required|min:2|max:50|regex:/^[a-zA-ZÀ-ÿ\s\-\']+$/',
            'prenom' => 'nullable|max:50|regex:/^[a-zA-ZÀ-ÿ\s\-\']*$/',
            'civilite' => 'required|in:' . implode(',', Patient::CIVILITES), 
            'telephone' => 'nullable|regex:/^[0-9+\-\s()]{8,15}$/',
            'email' => 'nullable|email|max:255',
            'adresse' => 'nullable|string|max:255',
        ], [
            'nom.required' => 'Le nom est obligatoire',
            'nom.regex' => 'Le nom ne doit contenir que des lettres',
            'civilite.required' => 'La civilité est obligatoire',
            'civilite.in' => 'Civilité non valide',
            'telephone.regex' => 'Format de téléphone invalide',
            'email.email' => 'Format email invalide'
        ]);
        
        try {
            if ($this->patient) {
                $this->patient->update([
                    'nom' => ucwords(strtolower(trim($this->nom))),
                    'prenom' => ucwords(strtolower(trim($this->prenom))),
                    'civilite' => $this->civilite,
                    'telephone' => trim($this->telephone),
                    'email' => strtolower(trim($this->email)),
                    'adresse' => trim($this->adresse),
                ]);
                
                flash()->success("Informations du patient « {$this->patient->nom} {$this->patient->prenom} » mises à jour");
            } else {
                $this->patient = Patient::create([
                    'nom' => ucwords(strtolower(trim($this->nom))),
                    'prenom' => ucwords(strtolower(trim($this->prenom))),
                    'civilite' => $this->civilite,
                    'telephone' => trim($this->telephone),
                    'email' => strtolower(trim($this->email)),
                    'adresse' => trim($this->adresse),
                ]);
                
                flash()->success("Nouveau patient « {$this->patient->nom} {$this->patient->prenom} » créé avec succès");
            }
            
            $this->nouveauPatient = false;
            $this->sauvegarderSession();
            $this->allerEtape('clinique');
            
        } catch (\Exception $e) {
            flash()->error('Erreur lors de ' . ($this->patient ? 'la modification' : 'la création') . ' du patient: ' . $e->getMessage());
        }
    }

    public function getCivilitesDisponiblesProperty()
    {
        return [
            'Madame' => [
                'label' => '👩 Mme',
                'genre' => 'F',
                'type' => 'adulte'
            ],
            'Monsieur' => [
                'label' => '👨 M.',
                'genre' => 'M', 
                'type' => 'adulte'
            ],
            'Mademoiselle' => [
                'label' => '👧 Mlle',
                'genre' => 'F',
                'type' => 'adulte'
            ],
            'Enfant-garçon' => [
                'label' => '👦 Garçon',
                'genre' => 'M',
                'type' => 'enfant'
            ],
            'Enfant-fille' => [
                'label' => '👧 Fille', 
                'genre' => 'F',
                'type' => 'enfant'
            ]
        ];
    }

    // ===================================== 
    // (Le reste des méthodes reste identique)
    // =====================================
    
    public function validerInformationsCliniques()
    {
        $this->validate([
            'prescripteurId' => 'required|exists:prescripteurs,id',
            'age' => 'required|integer|min:0|max:150',
            'patientType' => 'required|in:HOSPITALISE,EXTERNE,URGENCE-NUIT,URGENCE-JOUR',
            'poids' => 'nullable|numeric|min:0|max:500',
            'dateNaissance' => 'nullable|date|before:today|after:1900-01-01'
        ], [
            'prescripteurId.required' => 'Veuillez sélectionner un prescripteur',
            'prescripteurId.exists' => 'Prescripteur invalide',
            'age.required' => 'L\'âge est obligatoire',
            'age.min' => 'L\'âge doit être positif',
            'age.max' => 'L\'âge ne peut pas dépasser 150 ans',
            'poids.max' => 'Le poids ne peut pas dépasser 500 kg',
            'dateNaissance.date' => 'Format de date invalide',
            'dateNaissance.before' => 'La date de naissance doit être dans le passé'
        ]);
        
        try {
            if ($this->patient && $this->dateNaissance) {
                $this->patient->date_naissance = $this->dateNaissance;
                $this->patient->save();
                $this->patient->refresh();
                
                Log::info('Date naissance sauvegardée', [
                    'patient_id' => $this->patient->id,
                    'date_naissance' => $this->dateNaissance
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Erreur sauvegarde date naissance', [
                'error' => $e->getMessage(),
                'patient_id' => $this->patient?->id
            ]);
        }
        
        flash()->success('Informations cliniques validées');
        $this->allerEtape('analyses');
    }

    // =====================================
    // 🧪 ÉTAPE 3: SÉLECTION ANALYSES
    // =====================================
    
    public function toggleCategorie(int $categorieId)
    {
        $this->categorieOuverte = $this->categorieOuverte === $categorieId ? null : $categorieId;
    }
    
    public function ajouterAnalyse(int $analyseId)
    {
        if (isset($this->analysesPanier[$analyseId])) {
            flash()->warning('Analyse déjà ajoutée au panier');
            return;
        }

        try {
            $analyse = Analyse::with(['parent', 'enfants'])->find($analyseId);
            
            if (!$analyse) {
                flash()->error('Analyse introuvable');
                return;
            }

            if ($analyse->level === 'PARENT') {
                $this->ajouterAnalyseParent($analyse);
            } else {
                $this->ajouterAnalyseIndividuelle($analyse);
            }

            $this->calculerTotaux();
            $this->sauvegarderSession();
            
        } catch (\Exception $e) {
            flash()->error('Erreur lors de l\'ajout de l\'analyse');
            Log::error('Erreur ajout analyse', ['error' => $e->getMessage(), 'analyse_id' => $analyseId]);
        }
    }

    private function ajouterAnalyseParent($analyse)
    {
        if ($analyse->prix <= 0) {
            flash()->error('Ce panel n\'a pas de prix défini');
            return;
        }

        $enfantsDejaPresents = [];
        foreach ($this->analysesPanier as $id => $item) {
            if ($item['parent_id'] == $analyse->id) {
                $enfantsDejaPresents[] = $item['designation'];
            }
        }

        if (!empty($enfantsDejaPresents)) {
            flash()->warning('Certaines analyses de ce panel sont déjà sélectionnées: ' . implode(', ', $enfantsDejaPresents));
            return;
        }

        $this->analysesPanier[$analyse->id] = [
            'id' => $analyse->id,
            'designation' => $analyse->designation,
            'prix_original' => $analyse->prix,
            'prix_effectif' => $analyse->prix,
            'prix_affiche' => $analyse->prix,
            'prix' => $analyse->prix,
            'parent_nom' => 'Panel complet',
            'code' => $analyse->code,
            'parent_id' => null,
            'is_parent' => true,
            'enfants_inclus' => $analyse->enfants->pluck('designation')->toArray(),
        ];

        $message = "Panel « {$analyse->designation} » ajouté au panier";
        if ($analyse->enfants->count() > 0) {
            $message .= " (inclut {$analyse->enfants->count()} analyses)";
        }
        
        flash()->success($message);
    }

    private function ajouterAnalyseIndividuelle($analyse)
    {
        if (!in_array($analyse->level, ['NORMAL', 'CHILD'])) {
            flash()->error('Type d\'analyse non valide');
            return;
        }

        if ($analyse->parent_id) {
            foreach ($this->analysesPanier as $item) {
                if ($item['id'] == $analyse->parent_id && isset($item['is_parent'])) {
                    flash()->warning("Cette analyse est déjà incluse dans le panel « {$item['designation']} »");
                    return;
                }
            }
        }

        $prixEffectif = $analyse->prix;
        $parentNom = 'Analyse individuelle';

        if ($analyse->parent && $analyse->parent->prix > 0) {
            $parentNom = $analyse->parent->designation . ' (partie)';
        } elseif ($analyse->parent) {
            $parentNom = $analyse->parent->designation;
        }

        $this->analysesPanier[$analyse->id] = [
            'id' => $analyse->id,
            'designation' => $analyse->designation,
            'prix_original' => $analyse->prix,
            'prix_effectif' => $prixEffectif,
            'prix_affiche' => $prixEffectif,
            'prix' => $prixEffectif,
            'parent_nom' => $parentNom,
            'code' => $analyse->code,
            'parent_id' => $analyse->parent_id,
            'is_parent' => false,
        ];

        flash()->success("Analyse « {$analyse->designation} » ajoutée au panier");
    }

    public function retirerAnalyse(int $analyseId)
    {
        if (isset($this->analysesPanier[$analyseId])) {
            $nom = $this->analysesPanier[$analyseId]['designation'];
            unset($this->analysesPanier[$analyseId]);
            $this->calculerTotaux();
            $this->sauvegarderSession();
            flash()->info("Analyse « {$nom} » retirée du panier");
        }
    }
    
    public function validerAnalyses()
    {
        if (empty($this->analysesPanier)) {
            flash()->error('Veuillez sélectionner au moins une analyse');
            return;
        }

        $conflits = $this->detecterConflitsParentEnfant();
        if (!empty($conflits)) {
            flash()->error('Conflits détectés: ' . implode(', ', $conflits));
            return;
        }
        
        flash()->success(count($this->analysesPanier) . ' analyse(s) sélectionnée(s)');
        $this->allerEtape('prelevements');
    }

    private function detecterConflitsParentEnfant()
    {
        $conflits = [];
        $parentsPresents = [];
        $enfantsPresents = [];

        foreach ($this->analysesPanier as $analyse) {
            if (isset($analyse['is_parent']) && $analyse['is_parent']) {
                $parentsPresents[] = $analyse['id'];
            } else {
                if ($analyse['parent_id']) {
                    $enfantsPresents[$analyse['parent_id']][] = $analyse['designation'];
                }
            }
        }

        foreach ($parentsPresents as $parentId) {
            if (isset($enfantsPresents[$parentId])) {
                $parent = Analyse::find($parentId);
                $conflits[] = "Panel {$parent->designation} en conflit avec ses analyses individuelles";
            }
        }

        return $conflits;
    }

    // =====================================
    // 🧾 ÉTAPE 4: SÉLECTION PRÉLÈVEMENTS 
    // =====================================
    
    public function ajouterPrelevement(int $prelevementId)
    {
        if (isset($this->prelevementsSelectionnes[$prelevementId])) {
            flash()->warning('Prélèvement déjà ajouté');
            return;
        }

        try {
            $prelevement = Prelevement::find($prelevementId);
            
            if (!$prelevement) {
                flash()->error('Prélèvement introuvable');
                return;
            }
            
            $this->prelevementsSelectionnes[$prelevementId] = [
                'id' => $prelevement->id,
                'nom' => $prelevement->denomination,
                'description' => $prelevement->code ?? '',
                'prix' => $prelevement->prix ?? 0,
                'quantite' => 1,
                'type_tube_requis' => 'SEC',
                'volume_requis_ml' => 5.0,
            ];

            $this->calculerTotaux();
            $this->sauvegarderSession();
            flash()->success("Prélèvement « {$prelevement->denomination} » ajouté");
            
        } catch (\Exception $e) {
            flash()->error('Erreur lors de l\'ajout du prélèvement');
            Log::error('Erreur ajout prélèvement', ['error' => $e->getMessage(), 'prelevement_id' => $prelevementId]);
        }
    }
    
    public function retirerPrelevement(int $prelevementId)
    {
        if (isset($this->prelevementsSelectionnes[$prelevementId])) {
            $nom = $this->prelevementsSelectionnes[$prelevementId]['nom'];
            unset($this->prelevementsSelectionnes[$prelevementId]);
            $this->calculerTotaux();
            $this->sauvegarderSession();
            flash()->info("Prélèvement « {$nom} » retiré");
        }
    }
    
    public function modifierQuantitePrelevement(int $prelevementId, int $quantite)
    {
        if (isset($this->prelevementsSelectionnes[$prelevementId]) && $quantite > 0 && $quantite <= 10) {
            $this->prelevementsSelectionnes[$prelevementId]['quantite'] = $quantite;
            $this->calculerTotaux();
            $this->sauvegarderSession();
            flash()->info('Quantité mise à jour');
        }
    }
    
    public function validerPrelevements()
    {
        if (empty($this->prelevementsSelectionnes)) {
            flash()->info('Aucun prélèvement sélectionné - Passage direct au paiement');
        } else {
            flash()->success(count($this->prelevementsSelectionnes) . ' prélèvement(s) ajouté(s)');
        }

        $this->allerEtape('paiement');
    }

    // =====================================
    // 💰 ÉTAPE 5: PAIEMENT
    // =====================================
    
    public function validerPaiement()
    {
        $codesMethodesActives = PaymentMethod::where('is_active', true)
                                            ->pluck('code')
                                            ->toArray();
        
        $codesValidation = !empty($codesMethodesActives) 
            ? 'in:' . implode(',', $codesMethodesActives)
            : 'in:ESPECES,CARTE,CHEQUE,MOBILEMONEY';
        
        $this->validate([
            'modePaiement' => "required|{$codesValidation}",
            'montantPaye' => 'required|numeric|min:0',
            'remise' => 'nullable|numeric|min:0',
        ], [
            'modePaiement.required' => 'Veuillez sélectionner un mode de paiement',
            'modePaiement.in' => 'Mode de paiement non valide ou inactivé',
            'montantPaye.required' => 'Le montant payé est obligatoire',
            'montantPaye.min' => 'Le montant payé doit être positif',
        ]);

        if ($this->montantPaye < $this->total) {
            flash()->error('Montant payé insuffisant. Total: ' . number_format($this->total, 0) . ' Ar');
            return;
        }

        if (empty($this->analysesPanier)) {
            flash()->error('Aucune analyse sélectionnée');
            return;
        }
        
        $this->enregistrerPrescription();
    }

    public function changerStatutPaiement($nouveauStatut)
    {
        $this->paiementStatut = $nouveauStatut;
        
        if ($nouveauStatut) {
            flash()->info('Paiement sera marqué comme payé avec date automatique lors de l\'enregistrement.');
        } else {
            flash()->info('Paiement sera marqué comme non payé (sans date).');
        }
        
        $this->sauvegarderSession();
    }

    public function getStatutPaiementLibelle()
    {
        return $this->paiementStatut ? 'Payé' : 'Non Payé';
    }

    public function getStatutPaiementCouleur()
    {
        return $this->paiementStatut ? 'green' : 'red';
    }

    // =====================================
    // 🧪 ÉTAPE 6: TUBES
    // =====================================
    
    public function terminerPrescription()
    {
        $this->allerEtape('confirmation');
        
        $message = 'Nouvelle prescription enregistrée';
        
        if (!empty($this->tubesGeneres)) {
            $message .= ' - ' . count($this->tubesGeneres) . ' tube(s) généré(s)';
        }
        
        $this->viderSession();
        
        session()->flash('success', $message);
    }

    // =====================================
    // 📄 MÉTHODES POUR LA FACTURE
    // =====================================
    
    public function ouvrirFacture()
    {
        if (!$this->prescription) {
            flash()->error('Aucune prescription disponible');
            return;
        }
        
        $url = route('secretaire.prescription.facture', $this->prescription->id);
        $this->dispatch('open-window', ['url' => $url]);
    }
    
    public function imprimerFacture()
    {
        if (!$this->prescription) {
            flash()->error('Aucune prescription disponible');
            return;
        }
        
        $url = route('secretaire.prescription.facture', $this->prescription->id) . '?print=1';
        $this->dispatch('open-window', ['url' => $url]);
    }
    
    public function telechargerFacturePDF()
    {
        if (!$this->prescription) {
            flash()->error('Aucune prescription disponible');
            return;
        }
        
        $this->ouvrirFacture();
    }

    // =====================================
    // 📊 COMPUTED PROPERTIES
    // =====================================
    
    public function getCategoriesAnalysesProperty()
    {
        return Analyse::where('level', 'PARENT')
                     ->where('status', true)
                     ->with(['enfants' => function($query) {
                         $query->where('status', true)
                               ->whereIn('level', ['NORMAL', 'CHILD'])
                               ->orderBy('ordre')
                               ->orderBy('designation');
                     }])
                     ->orderBy('ordre')
                     ->orderBy('designation')
                     ->get();
    }

    public function getAnalysesRechercheProperty()
    {
        if (strlen($this->rechercheAnalyse) < 2) {
            $this->parentRecherche = null;
            return collect();
        }

        $terme = trim(strtoupper($this->rechercheAnalyse));
        $resultats = collect();
        
        $analysesChildDirectes = Analyse::where('status', true)
                        ->whereIn('level', ['CHILD', 'NORMAL'])
                        ->where('prix', '>', 0)
                        ->where(function($query) use ($terme) {
                            $query->whereRaw('UPPER(code) LIKE ?', ["%{$terme}%"])
                                ->orWhereRaw('UPPER(designation) LIKE ?', ["%{$terme}%"]);
                        })
                        ->with('parent')
                        ->get()
                        ->map(function($analyse) {
                            $analyse->recherche_directe = true;
                            return $analyse;
                        });

        $resultats = $resultats->concat($analysesChildDirectes);
        
        $parentsPayants = Analyse::where('status', true)
                        ->where('level', 'PARENT')
                        ->where('prix', '>', 0)
                        ->where(function($query) use ($terme) {
                            $query->whereRaw('UPPER(code) LIKE ?', ["%{$terme}%"])
                                ->orWhereRaw('UPPER(designation) LIKE ?', ["%{$terme}%"]);
                        })
                        ->get()
                        ->map(function($analyse) {
                            $analyse->recherche_directe = false;
                            return $analyse;
                        });

        $resultats = $resultats->concat($parentsPayants);

        $parentsGratuits = Analyse::where('status', true)
                        ->where('level', 'PARENT')
                        ->where(function($query) {
                            $query->where('prix', 0)->orWhereNull('prix');
                        })
                        ->where(function($query) use ($terme) {
                            $query->whereRaw('UPPER(code) LIKE ?', ["%{$terme}%"])
                                ->orWhereRaw('UPPER(designation) LIKE ?', ["%{$terme}%"]);
                        })
                        ->with(['enfants' => function($query) {
                            $query->where('status', true);
                        }])
                        ->get();

        foreach ($parentsGratuits as $parentGratuit) {
            if ($parentGratuit->enfants->count() > 0) {
                $this->parentRecherche = $parentGratuit;
                
                $enfantsPayants = $parentGratuit->enfants
                                ->where('status', true)
                                ->where('prix', '>', 0)
                                ->map(function($analyse) {
                                    $analyse->recherche_directe = false;
                                    return $analyse;
                                });
                
                $resultats = $resultats->concat($enfantsPayants);
            }
        }

        $individuelles = Analyse::where('status', true)
                            ->whereIn('level', ['NORMAL'])
                            ->where('prix', '>', 0)
                            ->where(function($query) use ($terme) {
                                $query->whereRaw('UPPER(code) LIKE ?', ["%{$terme}%"])
                                    ->orWhereRaw('UPPER(designation) LIKE ?', ["%{$terme}%"]);
                            })
                            ->with('parent')
                            ->get()
                            ->filter(function($analyse) {
                                return !$analyse->parent || 
                                    ($analyse->parent && $analyse->parent->prix <= 0);
                            })
                            ->map(function($analyse) {
                                $analyse->recherche_directe = false;
                                return $analyse;
                            });

        $resultats = $resultats->concat($individuelles);

        $resultatsUniques = $resultats->unique('id')->values();

        return $resultatsUniques->sortBy([
            function($analyse) {
                return isset($analyse->recherche_directe) && $analyse->recherche_directe ? 0 : 1;
            },
            function($analyse) use ($terme) {
                if (strtoupper($analyse->code) === $terme) return 1;
                if (str_starts_with(strtoupper($analyse->code), $terme)) return 2;
                if (str_contains(strtoupper($analyse->designation), $terme)) return 3;
                return 4;
            }
        ])->take(20);
    }

    public function getPrelevementsDisponiblesProperty()
    {
        return Prelevement::where('is_active', true)
                         ->orderBy('denomination')
                         ->get();
    }

    public function getPrelevementsRechercheProperty()
    {
        if (strlen($this->recherchePrelevement) < 2) {
            return collect();
        }

        return Prelevement::where('is_active', true)
                         ->where(function($query) {
                             $query->where('denomination', 'like', "%{$this->recherchePrelevement}%")
                                   ->orWhere('code', 'like', "%{$this->recherchePrelevement}%");
                         })
                         ->orderBy('denomination')
                         ->limit(10)
                         ->get();
    }
    
    public function enregistrerPrescription()
    {
        try {
            DB::beginTransaction();
            
            if (!$this->patient) {
                throw new \Exception('Patient non défini');
            }

            if (!Prescripteur::find($this->prescripteurId)) {
                throw new \Exception('Prescripteur invalide');
            }
            
            // 1. Créer la prescription
            $prescription = Prescription::create([
                'patient_id' => $this->patient->id,
                'prescripteur_id' => $this->prescripteurId,
                'secretaire_id' => Auth::user()->id,
                'patient_type' => $this->patientType,
                'age' => $this->age,
                'unite_age' => $this->uniteAge,
                'poids' => $this->poids,
                'renseignement_clinique' => $this->renseignementClinique,
                'remise' => $this->remise,
                'status' => 'EN_ATTENTE'
            ]);
            
            $this->prescription = $prescription;
            $this->reference = $prescription->reference;
            
            // 2. Associer les analyses
            $analyseIds = array_keys($this->analysesPanier);
            $analysesExistantes = Analyse::whereIn('id', $analyseIds)->pluck('id')->toArray();
            
            if (count($analysesExistantes) !== count($analyseIds)) {
                throw new \Exception('Certaines analyses sélectionnées n\'existent plus');
            }
            
            $prescription->analyses()->sync($analysesExistantes);
            
            // 3. Enregistrer le paiement
            $paymentMethod = PaymentMethod::where('code', $this->modePaiement)->first();
            
            if (!$paymentMethod) {
                throw new \Exception('Méthode de paiement invalide: ' . $this->modePaiement);
            }
            
            $paiementData = [
                'prescription_id' => $prescription->id,
                'montant' => $this->total,
                'payment_method_id' => $paymentMethod->id,
                'recu_par' => Auth::user()->id,
                'status' => $this->paiementStatut
            ];
            
            if ($this->paiementStatut) {
                $paiementData['date_paiement'] = now();
            }
            
            $paiement = Paiement::create($paiementData);
            
            // 4. Générer les tubes
            if (!empty($this->prelevementsSelectionnes)) {
                $this->tubesGeneres = $this->genererTubesDirectement($prescription);
                $this->allerEtape('tubes');
            } else {
                $this->allerEtape('confirmation');
            }
            
            DB::commit();
            $this->viderSession();
            
            $messageSucces = 'Prescription enregistrée avec succès!';
            if ($this->paiementStatut) {
                $messageSucces .= ' Paiement marqué comme payé avec date automatique.';
            }
            
            flash()->success($messageSucces);
            
        } catch (\Exception $e) {
            DB::rollBack();
            flash()->error('Erreur lors de l\'enregistrement: ' . $e->getMessage());
        }
    }

    private function genererTubesDirectement($prescription)
    {
        $tubes = [];
        
        try {
            $compteurTube = 1;
            
            foreach ($this->prelevementsSelectionnes as $prelevementData) {
                $prelevement = Prelevement::find($prelevementData['id']);
                
                if (!$prelevement) {
                    continue;
                }
                
                $quantite = max(1, $prelevementData['quantite'] ?? 1);
                
                for ($i = 0; $i < $quantite; $i++) {
                    $tube = Tube::create([
                        'prescription_id' => $prescription->id,
                        'patient_id' => $prescription->patient_id,
                        'prelevement_id' => $prelevement->id,
                        'code_barre' => $prescription->reference . '-T' . str_pad($compteurTube, 2, '0', STR_PAD_LEFT),
                    ]);

                    $tubes[] = [
                        'id' => $tube->id,
                        'numero_tube' => $tube->numero_tube,
                        'code_barre' => $tube->code_barre,
                        'statut' => $tube->statut,
                        'prelevement_nom' => $prelevement->denomination
                    ];
                    
                    $compteurTube++;
                }
            }

            $prescription->update(['status' => 'EN_ATTENTE']);
            flash()->success(count($tubes) . ' tube(s) généré(s) avec succès');
            
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la génération des tubes: ' . $e->getMessage());
        }

        return $tubes;
    }

    private function calculerTotaux()
    {
        try {
            $sousTotal = 0;
            $parentsTraites = [];

            foreach ($this->analysesPanier as $analyse) {
                if (isset($analyse['is_parent']) && $analyse['is_parent']) {
                    $sousTotal += $analyse['prix_effectif'];
                } else {
                    $sousTotal += $analyse['prix_effectif'];
                }
            }

            $totalPrelevements = 0;
            foreach ($this->prelevementsSelectionnes as $prelevement) {
                $totalPrelevements += ($prelevement['prix'] ?? 0) * ($prelevement['quantite'] ?? 1);
            }

            $this->total = max(0, $sousTotal + $totalPrelevements - $this->remise);
            
            if ($this->montantPaye < $this->total) {
                $this->montantPaye = $this->total;
            }
            
            $this->calculerMonnaie();
            
        } catch (\Exception $e) {
            $this->total = 0;
            $this->montantPaye = 0;
        }
    }
    
    private function calculerMonnaie()
    {
        $this->monnaieRendue = max(0, $this->montantPaye - $this->total);
    }

    // Computed properties
    public function getPatientsResultatsProperty()
    {
        if (strlen($this->recherchePatient) < 2) {
            return collect();
        }
        
        $terme = trim($this->recherchePatient);
        
        return Patient::where(function($query) use ($terme) {
                    $query->where('nom', 'like', "%{$terme}%")
                          ->orWhere('prenom', 'like', "%{$terme}%")
                          ->orWhere('telephone', 'like', "%{$terme}%")
                          ->orWhere('numero_dossier', 'like', "%{$terme}%");
                })
                ->orderBy('nom')
                ->limit(10)
                ->get();
    }

    public function getPrescripteursProperty()
    {
        return Prescripteur::where('is_active', true)
                          ->orderBy('nom')
                          ->get();
    }

    public function render()
    {
        return view('livewire.secretaire.prescription.form-prescription', [
            'patientsResultats' => $this->patientsResultats,
            'categoriesAnalyses' => $this->categoriesAnalyses,
            'analysesRecherche' => $this->analysesRecherche,
            'prescripteurs' => $this->prescripteurs,
            'prelevementsDisponibles' => $this->prelevementsDisponibles,
            'prelevementsRecherche' => $this->prelevementsRecherche,
        ]);
    }
}