<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('cart:prune-abandoned')]
#[Description('Purge unauthenticated guest cart items older than 30 days to optimize database storage')]
class PruneAbandonedCarts extends Command
{
    /**
     * The name and signature of the console command.
     * This is what you will type in your terminal to trigger it.
     */

    /**
     * The console command description.
     */

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Initiating abandoned guest cart pruning routine...');

        $deletedCount = DB::table('cart_items')
            ->whereNull('user_id')
            ->where('updated_at', '<', now()->subDays(30))
            ->delete();

        $this->info("Process completed successfully. Swept and purged {$deletedCount} abandoned line items from storage.");

        return self::SUCCESS;
    }
}
