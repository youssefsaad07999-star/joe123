<?php

namespace App\Livewire;

use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use App\Models\ShopSetting;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class CartPage extends Component
{
    public bool $isSidebar = false;

    #[On('cart-updated')]
    public function refreshCart()
    {
        // Simply triggers a visual re-render with fresh data
    }

    #[Computed]
    public function cartItems()
    {
        return $this->currentCartQuery()
            ->with(['variant.product', 'variant.size', 'variant.color', 'variant.product.images'])
            ->get();
    }

    #[Computed]
    public function cartTotal()
    {
        return $this->cartItems->sum->line_total;
    }

    #[Computed]
    public function freeShippingThreshold()
    {
        return ShopSetting::get('free_shipping_threshold');
    }

    #[Computed]
    public function standardShippingMethod()
    {
        return ShippingMethod::where('name', 'Standard Shipping')->first();
    }

    public function increment($itemId)
    {
        $item = CartItem::where('id', $itemId)
            ->with('variant')
            ->firstOrFail();

        $this->authorizeItem($item);

        if ($item->quantity >= $item->variant->stock_quantity) {
            // session()->flash('error', "Only {$item->variant->stock_quantity} units available.");
            $this->dispatch('notify', type: 'error', message: "Only {$item->variant->stock_quantity} units available, watch your cart.");

            return;
        }

        $item->increment('quantity');
        $this->dispatch('cart-updated');
    }

    public function decrement($itemId)
    {
        $item = CartItem::findOrFail($itemId);
        $this->authorizeItem($item);

        if ($item->quantity > 1) {
            $item->decrement('quantity');
        } else {
            $this->removeItem($itemId);
        }
        $this->dispatch('cart-updated');
    }

    public function removeItem($itemId)
    {
        $item = CartItem::find($itemId);
        if (empty($item)) {
            return;
        }
        $this->authorizeItem($item);

        $item->delete();
        $this->dispatch('notify', type: 'success', message: 'Item removed from the bag.');

        $this->dispatch('cart-updated');
    }

    public function clearCart()
    {
        $currentCart = $this->currentCartQuery();
        if (! $currentCart) {
            return;
        }
        $currentCart->delete();

        $this->dispatch('notify', type: 'success', message: 'Cart cleared successfully.');
        $this->dispatch('cart-updated');
    }

    private function currentCartQuery()
    {
        $query = CartItem::query();

        return auth()->check() ? $query->forUser(auth()->id()) : $query->forSession(session()->getId());
    }

    private function authorizeItem(CartItem $item): void
    {
        $ok = auth()->check() ? $item->user_id === auth()->id() : $item->session_id === session()->getId();

        abort_if(! $ok, 403);
    }

    public function render()
    {
        return $this->isSidebar ? view('livewire.cart-sidebar') : view('livewire.cart-page');
    }

    #[On('add-to-cart')]
    public function handleAddToCart($variantId, $quantity = 1)
    {
        if (! $variantId) {
            return;
        }

        $variant = ProductVariant::findOrFail($variantId);

        $existingCart = $this->currentCartQuery()
            ->where('product_variant_id', $variant->id)
            ->first();

        $newQty = ($existingCart?->quantity ?? 0) + $quantity;

        if ($newQty > $variant->stock_quantity) {
            $this->dispatch('notify', type: 'error', message: "Only {$variant->stock_quantity} units available, watch your cart.");

            return;
        }

        if ($existingCart) {
            $existingCart->update(['quantity' => $newQty]);
        } else {
            CartItem::create([
                'product_variant_id' => $variant->id,
                'user_id' => auth()->id() ?? null,
                'session_id' => auth()->check() ? null : session()->getId(),
                'quantity' => $newQty,
            ]);
        }

        $this->dispatch('notify', type: 'success', message: 'Item Added To the cart');
        $this->dispatch('cart-updated');
    }
}
