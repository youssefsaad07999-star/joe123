<x-layout>
    <div class="max-w-5xl mx-auto px-6 py-12">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-10">
            <div class="flex items-center gap-4">
                <a href="{{ route('orders.index') }}"
                    class="w-9 h-9 border border-gray-200 rounded-full flex items-center justify-center
                      hover:border-[#C85C6E] hover:text-[#C85C6E] transition-colors flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.25em] uppercase mb-0.5">
                        Order #{{ $order->id }}
                    </p>
                    <h1 class="font-['Cormorant_Garamond'] text-3xl font-light">
                        {{ $order->created_at->format('F d, Y') }}
                    </h1>
                </div>
            </div>

            {{-- Status badge --}}
            <span
                class="self-start sm:self-center px-4 py-1.5 rounded-full text-xs font-semibold tracking-wide uppercase
{{ match ($order->status?->value ?? $order->status) {
    'delivered' => 'bg-emerald-100 text-emerald-700',
    'shipped' => 'bg-blue-100 text-blue-700',
    'processing' => 'bg-amber-100 text-amber-700',
    'cancelled' => 'bg-red-100 text-red-700',
    'refunded' => 'bg-rose-100 text-rose-700',
    default => 'bg-gray-100 text-gray-600',
} }}">
                {{ $order->status?->getLabel() ?? ucfirst($order->status?->value ?? $order->status) }}
            </span>
        </div>

        {{-- Status Timeline --}}

        {{-- 1. REFUNDED STATE: Display Refund Alert Card --}}
        @if (($order->status?->value ?? $order->status) === 'refunded')
            <div class="bg-rose-50 border border-rose-200 rounded-2xl p-6 mb-6 text-rose-800">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" />
                    </svg>
                    <div>
                        <h4 class="font-semibold text-sm">Order Refunded</h4>
                        <p class="text-xs text-rose-700 mt-1">
                            This order has been fully refunded
                            @if ($order->refunded_at)
                                on {{ \Carbon\Carbon::parse($order->refunded_at)->format('M d, Y') }}
                            @endif.
                        </p>
                        @if ($order->refund_reason)
                            <p class="text-xs text-rose-600 italic mt-2">
                                Reason: "{{ $order->refund_reason }}"
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 2. CANCELLED STATE: Display Cancelled Alert Card --}}
        @elseif (($order->status?->value ?? $order->status) === 'cancelled')
            <div class="bg-gray-900 text-white rounded-2xl p-5 mb-6 shadow-md relative overflow-hidden">
                <!-- Decorative background glow -->
                <div
                    class="absolute -right-8 -bottom-8 w-24 h-24 bg-red-500/10 rounded-full blur-xl pointer-events-none">
                </div>

                <div class="flex items-start justify-between gap-4 relative z-10">
                    <div class="flex items-start gap-3.5">
                        <div
                            class="flex-shrink-0 w-10 h-10 rounded-xl bg-red-500/20 text-red-400 flex items-center justify-center border border-red-500/30">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-sm text-white">Order Cancelled</h4>
                            <p class="text-xs text-gray-400 mt-1 leading-relaxed">
                                {{ $order->refund_reason ?? 'This order was cancelled and is no longer being processed.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. ACTIVE ORDER STATE: Display Your Exact Progress Timeline --}}
        @else
            <div class="bg-white rounded-2xl p-8 shadow-sm mb-6">
                @php
                    $statuses = ['pending', 'processing', 'shipped', 'delivered'];
                    $labels = ['Ordered', 'Processing', 'Shipped', 'Delivered'];
                    $currentStatus = $order->status?->value ?? $order->status;
                    $currentIdx =
                        array_search($currentStatus, $statuses) !== false ? array_search($currentStatus, $statuses) : 0;
                @endphp

                <div class="flex items-center">
                    @foreach ($statuses as $idx => $status)
                        {{-- Step --}}
                        <div class="flex flex-col items-center flex-shrink-0">
                            <div
                                class="w-8 h-8 rounded-full border-2 flex items-center justify-center transition-all
                        {{ $idx <= $currentIdx ? 'bg-[#C85C6E] border-[#C85C6E]' : 'bg-white border-gray-200' }}">
                                @if ($idx <= $currentIdx)
                                    <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <div class="w-2 h-2 rounded-full bg-gray-200"></div>
                                @endif
                            </div>
                            <span
                                class="mt-2 text-xs whitespace-nowrap
                         {{ $idx <= $currentIdx ? 'text-[#1C1C1C] font-medium' : 'text-gray-400' }}">
                                {{ $labels[$idx] }}
                            </span>
                        </div>

                        {{-- Connector --}}
                        @if (!$loop->last)
                            <div
                                class="flex-1 h-0.5 mx-2 mb-5
                        {{ $idx < $currentIdx ? 'bg-[#C85C6E]' : 'bg-gray-100' }}">
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <div class="grid lg:grid-cols-3 gap-6">

            {{-- Items --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="font-['Cormorant_Garamond'] text-xl font-semibold">Items Ordered</h2>
                        <span class="text-xs text-gray-400">
                            {{ $order->variants->count() }} {{ Str::plural('item', $order->variants->count()) }}
                        </span>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @foreach ($order->variants as $variant)
                            @php
                                $img = $variant->product->images->where('color_id', $variant->color_id)->first();

                            @endphp
                            <div class="flex gap-4 p-5">

                                {{-- Image --}}
                                <div class="w-20 h-24 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                                    @if ($img)
                                        <img src="{{ asset('storage/' . $img->image_path) }}"
                                            class="w-full h-full object-cover" alt="{{ $variant->product->name }}">
                                    @else
                                        <div
                                            class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200
                                                flex items-center justify-center">
                                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                {{-- Details --}}
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-medium text-sm text-[#1C1C1C]">
                                        {{ $variant->product->name }}
                                    </h3>
                                    <p class="text-gray-400 text-xs mt-1">
                                        @if ($variant->size)
                                            Size: {{ $variant->size->name }}
                                        @endif
                                        @if ($variant->color)
                                            · Color: {{ ucfirst($variant->color->name) }}
                                        @endif
                                    </p>
                                    <p class="text-gray-300 text-xs mt-0.5 font-mono">
                                        {{ $variant->sku }}
                                    </p>

                                    <div class="flex items-center justify-between mt-3">
                                        <span class="text-xs text-gray-500 bg-gray-50 px-2.5 py-1 rounded-full">
                                            Qty {{ $variant->pivot->quantity }}
                                        </span>
                                        <div class="text-right">
                                            <p class="font-semibold text-sm">
                                                ${{ number_format($variant->pivot->subtotal, 2) }}
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                ${{ number_format($variant->pivot->unit_price, 2) }} each
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-4">

                {{-- Order Summary --}}
                <div class="bg-white rounded-2xl p-5 shadow-sm">
                    <h3 class="font-['Cormorant_Garamond'] text-lg font-semibold mb-4">Summary</h3>
                    <div class="space-y-2.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Subtotal</span>
                            {{-- Subtotal = total_price - shipping_cost --}}
                            <span>${{ number_format($order->total_price - $order->shipping_cost, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Shipping</span>
                            <span class="{{ $order->shipping_cost == 0 ? 'text-emerald-600 font-medium' : '' }}">
                                {{ $order->shipping_cost > 0 ? '$' . number_format($order->shipping_cost, 2) : 'Free' }}
                            </span>
                        </div>
                        @if ($order->shipping_method_name)
                            <p class="text-xs text-gray-400 -mt-1">{{ $order->shipping_method_name }}</p>
                        @endif
                    </div>
                    <div class="border-t border-gray-100 mt-4 pt-4 flex justify-between font-semibold">
                        <span>Total</span>
                        <span class="text-base">${{ number_format($order->total_price, 2) }}</span>
                    </div>
                </div>

                {{-- Shipping Address --}}
                <div class="bg-white rounded-2xl p-5 shadow-sm">
                    <h3 class="font-semibold text-sm mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z
                                 M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Shipping Address
                    </h3>
                    <p class="text-sm text-gray-600 font-light leading-relaxed">
                        {{ $order->shipping_first_name }} {{ $order->shipping_last_name }}<br>
                        {{ $order->shipping_address }}
                        @if ($order->shipping_address2)
                            , {{ $order->shipping_address2 }}
                        @endif
                        <br>
                        {{ $order->shipping_city }}, {{ $order->shipping_postal_code }}<br>
                        {{ $order->shipping_country }}
                    </p>
                    @if ($order->shipping_phone)
                        <p class="text-xs text-gray-400 mt-2">{{ $order->shipping_phone }}</p>
                    @endif
                </div>

                {{-- Payment --}}
                @if ($order->payment)
                    <div class="bg-white rounded-2xl p-5 shadow-sm">
                        <h3 class="font-semibold text-sm mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Payment
                        </h3>
                        <p class="text-sm text-gray-600 font-light capitalize">
                            {{ str_replace('_', ' ', $order->payment?->method ?? 'Processing') }}
                        </p>

                        <span
                            class="inline-flex items-center gap-1 mt-2 text-xs px-2.5 py-1 rounded-full
                        {{ $order->payment->status->value === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            <span
                                class="w-1.5 h-1.5 rounded-full
                            {{ $order->payment->status->value === 'paid' ? 'bg-emerald-500' : 'bg-amber-500' }}">
                            </span>
                            {{ ucfirst($order->payment->status?->getLabel() ?? 'pending') }}
                        </span>
                @endif
            </div>

        </div>
    </div>
    </div>
</x-layout>
