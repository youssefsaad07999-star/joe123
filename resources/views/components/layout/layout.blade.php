<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'JOE — Fashion for Every You' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @paddleJS
</head>

<body class="bg-[#F7F3EE] text-[#1C1C1C] flex flex-col min-h-screen font-['DM_Sans']" x-data="{ cartOpen: false, mobileMenuOpen: false }"
    x-init="cartOpen = localStorage.getItem('cartOpen') === 'true';
    $watch('cartOpen', val => localStorage.setItem('cartOpen', val));">
    {{-- CART SIDEBAR OVERLAY --}}
    <div x-show="cartOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="cartOpen = false"
        class="fixed inset-0 bg-black/50 z-40" style="display:none;">
    </div>

    {{-- CART SIDEBAR --}}
    <livewire:cart-page :is-sidebar="true" />

    {{-- NAV --}}
    <livewire:navbar />

    {{-- MAIN CONTENT --}}
    <main class="grow">
        {{ $slot }}
    </main>

    {{-- FOOTER --}}
    <x-layout.footer />

    {{-- SUCCESS  NOTIFICATION --}}
    <div x-data="{ show: false, message: '' }"
        @notify.window="if ($event.detail.type === 'success') { message = $event.detail.message; show = true; setTimeout(() => show = false, 1000); }"
        x-init="if ('{{ session('success') }}') {
            message = '{{ session('success') }}';
            show = true;
            setTimeout(() => show = false, 3500);
        }" x-show="show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-6 right-6 bg-[#1C1C1C] text-white px-5 py-3.5 rounded-2xl shadow-xl text-sm font-medium z-50 flex items-center gap-3"
        style="display:none;">
        <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd" />
        </svg>
        <span x-text="message"></span>
    </div>

    {{-- ERROR NOTIFICATION --}}
    <div x-data="{ show: false, message: '' }"
        @notify.window="if ($event.detail.type === 'error') { message = $event.detail.message; show = true; setTimeout(() => show = false, 1000); }"
        x-init="if ('{{ session('error') }}') {
            message = '{{ session('error') }}';
            show = true;
            setTimeout(() => show = false, 4000);
        }" x-show="show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-6 right-6 bg-rose-600 text-white px-5 py-3.5 rounded-2xl shadow-xl text-sm font-medium z-50 flex items-center gap-3"
        style="display:none;">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                clip-rule="evenodd" />
        </svg>
        <span x-text="message"></span>
    </div>
</body>

</html>
