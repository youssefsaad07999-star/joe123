<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Models\CartItem;
use App\Models\Country;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use App\Models\ShopSetting;
use App\Notifications\OrderCreatedNotification;
use App\OrderStatus;
use App\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cartTotal = 0;

        $cartItems = CartItem::forUser(Auth::id())
            ->with(['variant.product.images'])
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        foreach ($cartItems as $cartItem) {
            $cartTotal += $cartItem->line_total;
        }

        $shipping_methods = ShippingMethod::all();

        $countries = Country::query()->active()->get();

        $free_shipping_threshold = ShopSetting::get('free_shipping_threshold');

        return view('checkout.index', compact(
            'cartItems',
            'cartTotal',
            'shipping_methods',
            'countries',
            'free_shipping_threshold'
        ));
    }

    // foreach ($cartItems as $item) {
    //             if ($item->quantity > $item->variant->stock_quantity) {
    //                 $item->delete(); // Remove the out-of-stock item from cart
    //                 return redirect()->route('cart.index')->with('error', "The item '{$item->variant->product->name}' is out of stock or does not have enough quantity.");
    //             }
    //         }
    public function store(Request $request)
    {

        $data = $request->validate([
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'first_name' => 'required|string|max:80',
            'last_name' => 'required|string|max:80',
            'address' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'shipping_method' => 'required|exists:shipping_methods,id',
            'payment_method' => 'required|in:card,cod',
        ]);

        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $shippingMethod = ShippingMethod::findOrFail($data['shipping_method']);
        $subtotal = $cartItems->sum(fn ($i) => $i->unit_price * $i->quantity);
        $shippingCost = $this->shippingCost($subtotal, $shippingMethod);
        $total = $subtotal + $shippingCost;

        try {
            $cartItemIdToRemove = null;
            // Create the local order inside a transaction
            $order = DB::transaction(function () use ($data, $cartItems, $shippingMethod, $shippingCost, $total, &$cartItemIdToRemove) {
                $validatedVariants = [];

                foreach ($cartItems as $item) {
                    $variant = ProductVariant::lockForUpdate()
                        ->where('is_active', true)
                        ->where('id', $item->product_variant_id)
                        ->first();

                    $message = match (true) {
                        ! $variant => 'A product in your cart is no longer available.',
                        $variant->stock_quantity === 0 => "{$variant->product->name} is out of stock.",
                        $variant->stock_quantity < $item->quantity => "{$variant->product->name} — only {$variant->stock_quantity} available.",
                        default => null,
                    };

                    if ($message) {
                        if ($variant && $variant->stock_quantity === 0) {
                            $cartItemIdToRemove = $item->id;
                        }
                        throw new InsufficientStockException($message);
                    }

                    // Cache the locked variant for step 2
                    $validatedVariants[$item->id] = $variant;
                }

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'address_id' => Auth::user()->addresses()->where('is_default', true)->value('id'),
                    'shipping_method_id' => $shippingMethod->id,
                    'total_price' => $total,
                    'status' => $data['payment_method'] === 'cod' ? OrderStatus::Processing : OrderStatus::Pending,
                    'shipping_first_name' => $data['first_name'],
                    'shipping_last_name' => $data['last_name'],
                    'shipping_address' => $data['address'],
                    'shipping_address2' => $data['address2'] ?? null,
                    'shipping_city' => $data['city'],
                    'shipping_country' => $data['country'],
                    'shipping_postal_code' => $data['postal_code'],
                    'shipping_phone' => $data['phone'],
                    'shipping_cost' => $shippingCost,
                ]);

                foreach ($cartItems as $item) {
                    $order->variants()->attach($item->product_variant_id, [
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'subtotal' => $item->unit_price * $item->quantity,
                    ]);

                    $variant = $validatedVariants[$item->id];
                    $newStock = $variant->stock_quantity - $item->quantity;

                    $variant->update([
                        'stock_quantity' => max(0, $newStock),
                    ]);

                }

                return $order;
            });
        } catch (InsufficientStockException $e) {
            if ($cartItemIdToRemove) {
                CartItem::destroy($cartItemIdToRemove);
            }

            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }

        $this->clearCart();

        // ── Cash on Delivery — no payment gateway ─────────────────────────
        if ($data['payment_method'] === 'cod') {

            $user = $order->user;
            $user->notify(new OrderCreatedNotification($order));

            $order->payment()->create([
                'user_id' => $order->user_id,
                'amount' => $total,
                'method' => $data['payment_method'] === 'cod' ? 'cash on delivery' : 'card',
                'status' => PaymentStatus::Pending,
                'transaction_id' => null,
            ]);

            return redirect()->route('checkout.success', $order)
                ->with('success', 'Order placed! Pay on delivery.');
        }

        // ── Paddle card checkout ───────────────────────────────────────────
        try {
            $priceId = env('PADDLE_UNIT_PRICE_ID'); // PADDLE_UNIT_PRICE_ID
            $totalCents = (int) round($total * 100);  // $15.50 → 1550

            $checkout = Auth::user()
                ->checkout([$priceId => $totalCents])  // price_id => quantity (cents)
                ->customData([                          // passed to webhook
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                ])
                ->returnTo(route('checkout.success', [$order]));

            return view('checkout.paddle', compact('checkout'));

        } catch (\Throwable $e) {
            $order->update(['status' => 'cancelled']);
            report($e);

            return back()->with('error', 'Could not initiate payment. Please try again.');
        }
    }

    public function success(Order $order)
    {
        abort_if($order->user_id !== Auth::id(), 403);

        return view('checkout.success', compact('order'));
    }

    private function clearCart(): void
    {
        CartItem::query()
            ->when(
                Auth::check(),
                fn ($q) => $q->forUser(Auth::id()),
                fn ($q) => $q->forSession(session()->getId())
            )->delete();
    }

    private function getCartItems()
    {
        return CartItem::query()
            ->with(['variant.product.fit', 'variant.size', 'variant.color'])
            ->when(
                Auth::check(),
                fn ($q) => $q->forUser(Auth::id()),
                fn ($q) => $q->forSession(session()->getId())
            )->get();
    }

    private function shippingCost(float $subtotal, ShippingMethod $method): float
    {
        return ($subtotal >= ShopSetting::get('free_shipping_threshold') && $method->name === 'Standard Shipping')
            ? 0
            : $method->price;
    }
}
