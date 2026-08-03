<?php

namespace App\Traits;

use App\Models\CartItem;
use Livewire\Attributes\Computed;

trait HasCart
{
    /**
     * Get total item count in cart.
     * Cached per component with #[Computed] and across the request with once().
     */
    #[Computed]
    public function cartCount(): int
    {
        return $this->cartItems()->count();
    }

    #[Computed]
    public function cartItems()
    {
        $cacheKey = 'request_cart_items_'.(auth()->id() ?? session()->getId());

        return cache()->driver('array')->remember($cacheKey, 60, function () {
            return $this->currentCartQuery()
                ->with(['variant.product', 'variant.size', 'variant.color', 'variant.product.images'])
                ->get();
        });
    }

    /**
     * Reusable query builder for cart items (User vs Session).
     */
    protected function currentCartQuery()
    {
        $query = CartItem::query();

        return auth()->check()
            ? $query->forUser(auth()->id())
            : $query->forSession(session()->getId());
    }
}
