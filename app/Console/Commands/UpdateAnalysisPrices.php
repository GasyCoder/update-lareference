<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Analyse;
use Illuminate\Support\Facades\File;

class UpdateAnalysisPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-analysis-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update analysis prices from storage/app/analyses_dump.txt';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = storage_path('app/analyses_dump.txt');
        if (!File::exists($path)) {
            $this->error("File not found: $path");
            return 1;
        }

        $content = File::get($path);

        // Regex to match:
        // ID (digits)
        // Code (non-space)
        // Level (PARENT/NORMAL/CHILD)
        // ... description (lazy match) ...
        // Price (digits.digits)
        // Is_bold (0 or 1)
        // Examen ID (digits)
        // Type ID (digits)
        // Matches across newlines (s modifier)
        $pattern = '/(?:^|\n)(\d+)\s+(?:\S+)\s+(?:PARENT|NORMAL|CHILD).*?(\d+\.\d{2})\s+[01]\s+\d+\s+\d+/s';

        $this->info("Parsing dump file...");
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        $dumpPrices = [];
        foreach ($matches as $match) {
            $id = (int) $match[1];
            $price = $match[2];
            $dumpPrices[$id] = $price;
        }

        $this->info("Found " . count($dumpPrices) . " records in dump.");

        if (empty($dumpPrices)) {
            $this->error("No records parsed. Check regex or file content.");
            return 1;
        }

        $analyses = Analyse::all();
        $updated = 0;
        $zeroed = 0;

        $this->output->progressStart($analyses->count());

        foreach ($analyses as $analyse) {
            if (isset($dumpPrices[$analyse->id])) {
                $newPrice = $dumpPrices[$analyse->id];
                // Compare as floats/string
                if (bccomp($analyse->prix, $newPrice, 2) !== 0) {
                    $analyse->prix = $newPrice;
                    $analyse->save();
                    $updated++;
                }
            } else {
                // Not in dump -> set to 0
                if ($analyse->prix != 0) {
                    $analyse->prix = 0;
                    $analyse->save();
                    $zeroed++;
                }
            }
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        $this->info("Update complete.");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Updated Prices', $updated],
                ['Set to Zero (Missing in Dump)', $zeroed],
                ['Total DB Records', $analyses->count()]
            ]
        );

        return 0;
    }
}
