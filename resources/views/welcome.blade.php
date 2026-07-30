<x-layout>

    {{-- HERO --}}
    <section class="relative min-h-[85vh] bg-[#1C1C1C] flex items-center overflow-hidden">
        {{-- Background Texture --}}
        <div class="absolute inset-0 opacity-20"
            style="background-image: radial-gradient(circle at 20% 50%, #C85C6E 0%, transparent 50%), radial-gradient(circle at 80% 20%, #8B6B8A 0%, transparent 40%);">
        </div>

        <div class="relative max-w-7xl mx-auto px-6 w-full">
            <div class="grid md:grid-cols-2 gap-12 items-center">

                {{-- Text --}}
                <div>
                    <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.3em] uppercase mb-4">New Season 2026</p>
                    <h1
                        class="font-['Cormorant_Garamond'] text-6xl md:text-7xl lg:text-8xl font-light text-white leading-none">
                        Dress<br>
                        <em class="italic text-[#C85C6E]">Your</em><br>
                        Story
                    </h1>
                    <p class="text-gray-400 mt-6 text-base font-light leading-relaxed max-w-sm">
                        Explore the latest collections crafted for the ones who dress with intention.
                        Men & Women's fashion that moves with you.
                    </p>

                    <div class="flex flex-wrap gap-4 mt-8">
                        @foreach ($genders as $gender)
                            <a href="{{ route('gender.index', ['gender' => $gender->slug]) }}"
                                class="group px-8 py-3.5 border {{ $loop->first ? 'bg-[#C85C6E] border-[#C85C6E] text-white hover:bg-transparent hover:text-[#C85C6E]' : 'border-white/30 text-white hover:border-white' }}
                                                      rounded-full text-sm font-medium tracking-wide transition-all duration-300">
                                Shop {{ $gender->name }}
                                <span class="ml-1 group-hover:ml-2 transition-all duration-200">→</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Hero Image --}}
                <div class="relative hidden md:block">a
                    <div
                        class="absolute -inset-4 bg-gradient-to-br from-[#C85C6E]/20 to-transparent rounded-3xl blur-xl">
                    </div>
                    <div class="h-140 w-150 relative rounded-3xl overflow-hidden aspect-[4/5] group">
                        <img src="{{ asset('storage/' . $landingImage) }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                            alt="Fashion Collection">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                    </div>

                    {{-- Floating Card --}}
                    {{-- <div class="absolute -bottom-6 -left-8 bg-white rounded-2xl p-4 shadow-2xl">
                        <p class="text-xs text-gray-500 font-medium">New In</p>
                        <p class="font-['Cormorant_Garamond'] text-xl font-semibold mt-0.5">Summer '26</p>
                        <div class="flex items-center gap-1 mt-1">
                            @for ($i = 0; $i < 5; $i++) <svg class="w-3 h-3 text-[#C85C6E]" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                @endfor
                                <span class="text-xs text-gray-500 ml-1">4.9/5</span>
                        </div>
                    </div> --}}
                </div>

            </div>
        </div>
    </section>

    {{-- GENDER CARDS --}}
    <section class="max-w-7xl mx-auto px-6 py-20">
        <div class="text-center mb-12">
            <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.3em] uppercase mb-3">Collections</p>
            <h2 class="font-['Cormorant_Garamond'] text-4xl md:text-5xl font-light">Shop by Style</h2>
        </div>
        <div class="grid md:grid-cols-2 gap-6">
            @foreach ($genders as $gender)
                <a href="{{ route('gender.index', ['gender' => $gender->slug]) }}"
                    class="group relative h-[400px] rounded-3xl overflow-hidden bg-gray-200 block">
                    <img src="{{ asset('storage/' . $gender->image_path) }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                        alt="{{ $gender->name }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>
                    <div class="absolute bottom-8 left-8 right-8">
                        <p class="text-white/70 text-xs tracking-[0.2em] uppercase mb-2">Explore</p>
                        <h3 class="font-['Cormorant_Garamond'] text-5xl font-light text-white">{{ $gender->name }}</h3>
                        <span
                            class="inline-flex items-center gap-2 mt-4 text-white text-sm border-b border-white/50 pb-0.5 group-hover:border-white transition-colors">
                            View Collection <span class="group-hover:translate-x-1 transition-transform">→</span>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- FEATURES --}}
    <section class="bg-[#1C1C1C] text-white py-14">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                @foreach ([
        [
            'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
            'title' => 'Free Shipping',
            'desc' => 'On orders over EGP ' . $freeShippingThreshold,
        ],
        [
            'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
            'title' => 'Easy Returns',
            'desc' => '30-day free returns',
        ],
        [
            'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
            'title' => 'Secure Payment',
            'desc' => '100% protected checkout',
        ],
        [
            'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
            'title' => 'Style Help',
            'desc' => 'Expert fashion advice',
        ],
    ] as $feature)
                    <div class="text-center">
                        <div
                            class="w-12 h-12 mx-auto mb-3 rounded-full border border-white/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#C85C6E]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="{{ $feature['icon'] }}" />
                            </svg>
                        </div>
                        <h4 class="font-medium text-sm">{{ $feature['title'] }}</h4>
                        <p class="text-gray-400 text-xs mt-1 font-light">{{ $feature['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</x-layout>
