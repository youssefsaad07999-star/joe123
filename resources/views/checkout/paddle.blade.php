<x-layout>
    <div class="max-w-2xl mx-auto py-16 px-4 text-center flex flex-col items-center justify-center min-h-[60vh]">

        <div class="w-full flex flex-col items-center justify-center space-y-6">

            <!-- Order Status Badge -->
            <div
                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-medium">
                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                Order #{{ $checkout->customData['order_id'] ?? 'Pending' }} Created
            </div>

            <!-- Main Title -->
            <div class="space-y-2">
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Completing Your Order</h1>
                <p class="text-sm text-gray-500">Opening secure checkout window...</p>
            </div>

            <!-- Paddle Checkout Button -->
            <div class="w-full max-w-sm">
                <x-paddle-button :checkout="$checkout" id="auto-pay-btn"
                    class="w-full inline-flex items-center justify-center gap-3 px-8 py-4 text-base font-semibold text-white bg-gradient-to-r from-indigo-600 to-violet-600 rounded-2xl shadow-lg shadow-indigo-100 hover:from-indigo-700 hover:to-violet-700 hover:shadow-xl hover:shadow-indigo-200 active:scale-[0.98] transition-all duration-300 group cursor-pointer">

                    <!-- Spinner (Hidden when clicked/ready) -->
                    <svg id="btn-spinner" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>

                    <!-- Lock Icon (Initial) -->
                    <svg id="btn-lock" class="hidden w-5 h-5 text-white/80" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>

                    <span id="btn-text">Securing Connection...</span>

                    <svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </x-paddle-button>
            </div>

            <!-- Notice Callout Card -->
            <div class="w-full max-w-md bg-amber-50/80 border border-amber-200/80 rounded-2xl p-4 text-left shadow-sm">
                <div class="flex gap-3">
                    <div
                        class="flex-shrink-0 w-8 h-8 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold uppercase tracking-wider text-amber-900">Important Notice</h4>
                        <p class="text-xs text-amber-800 mt-1 leading-relaxed">
                            Once payment is complete, <strong>please leave this window open</strong>. You will
                            automatically be redirected to your Order Confirmation page.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Manual Fallback & Cancel Route -->
            <div class="flex flex-col items-center gap-2 pt-2">
                <p class="text-xs text-gray-400">
                    If the popup didn't open automatically, click the button above.
                </p>
                <a href="{{ route('cart.index') }}"
                    class="text-xs text-gray-500 hover:text-gray-700 underline transition-colors">
                    Return to Cart
                </a>
            </div>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const payBtn = document.getElementById('auto-pay-btn');
            const btnSpinner = document.getElementById('btn-spinner');
            const btnLock = document.getElementById('btn-lock');
            const btnText = document.getElementById('btn-text');

            if (payBtn) {
                // 1. Small delay ensuring Paddle JS initialized event listeners
                setTimeout(() => {
                    payBtn.click();

                    // 2. Transform button state so it acts as "Pay Now" if closed/re-clicked
                    setTimeout(() => {
                        if (btnSpinner) btnSpinner.classList.add('hidden');
                        if (btnLock) btnLock.classList.remove('hidden');
                        if (btnText) btnText.textContent = 'Pay Now with Paddle';
                    }, 1200);
                }, 350);
            }
        });
    </script>
</x-layout>
