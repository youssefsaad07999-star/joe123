<?php

namespace App\Http\Controllers\ProductControllers;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartItemController extends Controller
{
    public function index()
    {
        $cartItems = $this->currentCart()
            ->with(['variant.product', 'variant.size', 'variant.color', 'variant.product.images'])
            ->get();

        $cartTotal = $cartItems->sum->line_total;

        $free_shipping_threshold = ShopSetting::get('free_shipping_threshold');

        $standardShippingMethod = ShippingMethod::where('name', 'Standard Shipping')->first();

        return view('cart.index', compact('cartItems', 'cartTotal', 'free_shipping_threshold', 'standardShippingMethod'));

    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'product_variant_id' => 'required',
            'quantity' => 'required|integer|min:1|max:50',
        ]);

        $variant = ProductVariant::findOrFail($data['product_variant_id']);

        // Check requested quantity is available
        $existing = $this->currentCart()
            ->where('product_variant_id', $variant->id)
            ->first();

        $newQty = ($existing?->quantity ?? 0) + $data['quantity'];

        if ($newQty > $variant->stock_quantity) {
            throw ValidationException::withMessages([
                'quantity' => "Only {$variant->stock_quantity} units are available in stock.",
            ]);
        }

        $this->currentCart()->updateOrCreate(
            [
                'product_variant_id' => $variant->id,
                'user_id' => auth()->id(),
                'session_id' => auth()->check() ? null : session()->getId(),
            ],
            [
                'quantity' => $newQty,
            ]
        );

        return back()->with('success', "{$variant->product->name} added to your bag.");
    }

    public function update(Request $request, CartItem $cartItem)
    {

        $this->authorizeItem($cartItem);

        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if ($cartItem->variant->stock_quantity < $data['quantity']) {
            throw ValidationException::withMessages([
                'quantity' => "Only {$cartItem->variant->stock_quantity} units are available in stock.",
            ]);
        }

        $cartItem->update([
            'quantity' => $data['quantity'],
        ]);

        return back()->with('success', 'Cart updated.');

    }

    public function destroy(CartItem $cartItem)
    {
        $this->authorizeItem($cartItem);

        $cartItem->delete();

        return back()->with('success', 'Item deleted successfully');
    }

    public function clear()
    {
        $this->currentCart()->delete();

        return back()->with('success', 'Cart cleared.');
    }

    public function currentCart()
    {
        $query = CartItem::query();

        return auth()->check()
        ? $query->forUser(auth()->id())
        : $query->forSession(session()->getId());
    }

    public function authorizeItem(CartItem $item): void
    {
        $ok = auth()->check()
        ? $item->user_id === auth()->id()
        : $item->session_id === session()->getId();

        abort_if(! $ok, 403);
    }
}
