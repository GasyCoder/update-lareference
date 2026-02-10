<?php

namespace App\Console\Commands;

use App\Models\Paiement;
use Illuminate\Console\Command;

class RestoreSoftDeletedPaiements extends Command
{
    protected $signature = 'paiements:restore-soft-deleted';
    protected $description = 'Restaurer les paiements qui ont été soft-deleted par erreur';

    public function handle()
    {
        $this->info('🔍 Recherche des paiements soft-deleted...');

        $paiementsSoftDeleted = Paiement::onlyTrashed()->get();
        $count = $paiementsSoftDeleted->count();

        if ($count === 0) {
            $this->info('✅ Aucun paiement soft-deleted trouvé.');
            return 0;
        }

        $this->warn("⚠️  {$count} paiement(s) soft-deleted trouvé(s).");

        if (!$this->confirm('Voulez-vous restaurer tous ces paiements ?', true)) {
            $this->info('❌ Opération annulée.');
            return 0;
        }

        $this->info('🔄 Restauration en cours...');
        $bar = $this->output->createProgressBar($count);

        $restored = 0;
        foreach ($paiementsSoftDeleted as $paiement) {
            try {
                $paiement->restore();
                $restored++;
                $bar->advance();
            } catch (\Exception $e) {
                $this->error("Erreur lors de la restauration du paiement ID {$paiement->id}: {$e->getMessage()}");
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ {$restored} paiement(s) restauré(s) avec succès !");

        // Afficher un résumé
        $this->table(
            ['Statut', 'Nombre'],
            [
                ['Restaurés', $restored],
                ['Échecs', $count - $restored],
                ['Total', $count]
            ]
        );

        return 0;
    }
}
