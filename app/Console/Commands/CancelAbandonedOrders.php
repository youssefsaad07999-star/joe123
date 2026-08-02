<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\OrderStatus;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

#[Signature('app:cancel-abandoned-orders {--minutes=15 : Timeout threshold in minutes}')]
#[Description('Cancel pending orders that exceeded the payment timeout threshold and restore stock')]
class CancelAbandonedOrders extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $minutes = (int) $this->option('minutes');
        $threshold = now()->subMinutes($minutes);

        // Fetch pending orders older than the threshold
        $expiredOrders = Order::where('status', OrderStatus::Pending)
            ->where('created_at', '<=', $threshold)
            ->with('variants')
            ->get();

        if ($expiredOrders->isEmpty()) {
            $this->info('No abandoned orders found.');

            return self::SUCCESS;
        }

        $count = 0;

        foreach ($expiredOrders as $order) {
            DB::transaction(function () use ($order) {
                // 1. Restore reserved inventory stock
                foreach ($order->variants as $variant) {
                    $variant->increment('stock_quantity', $variant->pivot->quantity);
                }

                // 2. Mark order as Cancelled (or Expired)
                $order->update([
                    'status' => OrderStatus::Cancelled,
                    'refund_reason' => 'Auto-cancelled due to payment inactivity/abandonment.',
                ]);
            });

            $count++;
        }

        Log::info("Auto-cancelled {$count} abandoned pending orders.");
        $this->info("Successfully cancelled {$count} abandoned orders.");

        return self::SUCCESS;
    }
}
