<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cotacao;
use Carbon\Carbon;

class UpdateCotacaoStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cotacao:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update cotacao status to 3 (Encerrado) if encerramento date is reached or passed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today();

        $cotacoesToUpdate = Cotacao::where('status', '!=', 3)
            ->whereDate('encerramento', '<=', $today)
            ->get();

        foreach ($cotacoesToUpdate as $cotacao) {
            $cotacao->status = 3;
            $cotacao->save();
            $this->info("Cotacao ID {$cotacao->id} status updated to 3 (Encerrado).");
        }

        $this->info('Cotacao status update job completed.');

        return 0;
    }
}
