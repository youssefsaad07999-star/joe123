<div x-show="cartOpen" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-x-full" x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 translate-x-full" style="display:none;"
    class="fixed right-0 top-0 h-full w-full max-w-sm bg-white z-50 flex flex-col shadow-2xl">

    {{-- Cart Header --}}
    <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
        <h2 class="font-['Cormorant_Garamond'] text-2xl font-semibold">Your Cart</h2>
        <button @click="cartOpen = false" class="p-2 hover:bg-gray-100 rounded-full transition" x-cloak>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Cart Items --}}
    <div class="flex-1 overflow-y-auto px-6 py-4">
        {{-- 💡 We use $this->cartItems from our shared CartPage class --}}
        @if ($this->cartItems->isNotEmpty())
            @foreach ($this->cartItems as $item)
                {{-- 💡 wire:key keeps tracking smooth when items change --}}
                <div wire:key="sidebar-item-{{ $item->id }}" class="flex gap-4 py-4 border-b border-gray-100">
                    <div class="w-20 h-24 bg-gray-100 rounded-lg overflow-hidden shrink-0">
                        @php
                            $img =
                                $item->variant?->product?->images->firstWhere('color_id', $item->variant->color_id) ??
                                $item->variant?->product?->primaryImage;
                        @endphp
                        @if ($img)
                            <img src="{{ asset('storage/' . $img->image_path) }}" class="w-full h-full object-cover"
                                alt="{{ $item->variant->product->name }}">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-sm truncate">{{ $item->variant->product->name }}</p>
                        <p class="text-gray-500 text-xs mt-0.5">{{ $item->variant->size->name ?? 'One Size' }} ·
                            {{ ucfirst($item->variant->color->name) ?? '' }}</p>

                        <div class="flex items-center justify-between mt-2">
                            <div class="flex items-center border border-gray-200 rounded-full bg-white">
                                {{-- 💡 wire:click triggers the backend decrement directly with no forms! --}}
                                <button type="button" wire:click="decrement({{ $item->id }})"
                                    class="w-7 h-7 flex items-center justify-center text-gray-500 hover:text-black transition"
                                    @disabled($item->quantity <= 1)>
                                    −
                                </button>
                                <span class="w-6 text-center text-sm font-medium">{{ $item->quantity }}</span>
                                {{-- 💡 wire:click triggers the backend increment directly --}}
                                <button type="button" wire:click="increment({{ $item->id }})"
                                    class="w-7 h-7 flex items-center justify-center text-gray-500 hover:text-black transition"
                                    @disabled($item->quantity >= $item->variant->stock_quantity)>
                                    +
                                </button>
                            </div>
                            <p class="font-semibold text-sm text-gray-900">
                                ${{ number_format($item->line_total, 2) }}
                            </p>
                        </div>
                    </div>
                    {{-- 💡 wire:click removes the item completely --}}
                    <button type="button" wire:click="removeItem({{ $item->id }})"
                        class="text-gray-300 hover:text-rose-500 transition h-fit mt-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endforeach
        @else
            <div class="flex flex-col items-center justify-center h-full text-center py-16">
                <svg class="w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <p class="text-gray-400 font-light">Your cart is empty</p>
                <a href="/" @click="cartOpen = false" class="mt-4 text-sm text-[#C85C6E] hover:underline">Continue
                    Shopping</a>
            </div>
        @endif
    </div>

    {{-- Cart Footer --}}
    @if ($this->cartItems->isNotEmpty())
        <div class="px-6 py-5 border-t border-gray-100 bg-gray-50">
            <div class="flex justify-between items-center mb-4">
                <span class="text-gray-600">Subtotal</span>
                <span class="font-semibold text-lg text-gray-900">${{ number_format($this->cartTotal ?? 0, 2) }}</span>
            </div>
            @auth
                <a href="{{ route('checkout.index') }}" @click="cartOpen = false"
                    class="block w-full bg-[#1C1C1C] text-white text-center py-3.5 rounded-full font-medium hover:bg-[#C85C6E] transition-colors duration-300 text-sm">
                    Proceed to Checkout
                </a>
                <a href="{{ route('cart.index') }}" @click="cartOpen = false"
                    class="block w-full text-center py-2.5 mt-2 text-sm text-gray-500 hover:text-black transition">
                    View Full Cart
                </a>
            @else
                <div class="space-y-2">
                    <a href="{{ route('login') }}"
                        class="block w-full bg-[#1C1C1C] text-white text-center py-4 rounded-full
                        font-medium hover:bg-[#C85C6E] transition-colors duration-300 text-sm"
                        @click="cartOpen = false">
                        Sign In to Checkout
                    </a>
                    <a href="{{ route('register') }}"
                        class="block w-full border border-gray-200 text-center py-3.5 rounded-full
                           text-sm text-gray-600 hover:border-gray-400 transition-colors"
                        @click="cartOpen = false">
                        Create an Account
                    </a>
                    <a href="{{ route('cart.index') }}" @click="cartOpen = false"
                        class="block w-full text-center py-2.5 mt-2 text-sm text-gray-500 hover:text-black transition">
                        View Full Cart
                    </a>
                    <p class="text-xs text-gray-400 text-center">
                        Your bag is saved — sign in to complete your order
                    </p>
                </div>
            @endauth
        </div>
    @endif
</div>
