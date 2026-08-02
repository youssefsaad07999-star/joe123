<?php

namespace App\Listeners;

use App\Models\Order;
use App\Models\Payment;
use App\Notifications\OrderCreatedNotification;
use App\OrderStatus;
use App\PaymentStatus;
use Laravel\Paddle\Events\TransactionCompleted;

class HandlePaddleTransactionCompleted
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TransactionCompleted $event): void
    {
        // $event->payload is the full Paddle webhook payload
        $payload = $event->payload;
        $customData = $payload['data']['custom_data'] ?? [];
        $orderId = $customData['order_id'] ?? null;

        if (! $orderId) {
            return;
        }

        $order = Order::with('variants')->find($orderId);

        if (! $order || ! in_array($order->status, [OrderStatus::Pending, OrderStatus::Cancelled], true)) {
            return;
        }

        // foreach ($order->variants as $variant) {
        //     $newStock = $variant->stock_quantity - $variant->pivot->quantity;
        //     $variant->update([
        //         'stock_quantity' => max(0, $newStock),
        //     ]);
        // }

        // Update order status
        $order->update(['status' => OrderStatus::Processing]);

        $user = $order->user;
        $user->notify(new OrderCreatedNotification($order));

        // Create or update the payment record
        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'user_id' => $order->user_id,
                'amount' => $payload['data']['details']['totals']['total'] / 100,
                'method' => 'card',
                'status' => PaymentStatus::Paid,
                'transaction_id' => $payload['data']['id'] ?? null,
            ]
        );
    }
}
