<header class="sticky top-0 z-30 bg-[#1C1C1C] text-white">
    <nav class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">

        {{-- LOGO --}}
        <a href="/"
            class="font-['Cormorant_Garamond'] text-2xl font-semibold tracking-widest hover:text-[#C85C6E] transition-colors">
            JOE
        </a>

        {{-- DESKTOP LINKS --}}
        <div class="hidden md:flex items-center gap-1">
            <a href="/"
                class="px-4 py-2 text-sm font-light tracking-wide hover:text-[#C85C6E] transition-colors
                      @active('home')
text-[#C85C6E]
@endactive">
                Home
            </a>

            @foreach ($navGenders as $navGender)
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">

                    {{-- Gender link --}}
                    <a href="{{ route('gender.index', $navGender, false) }}" @class([
                        'px-4 py-2 text-sm font-light tracking-wide hover:text-[#C85C6E] transition-colors flex items-center gap-1',
                        'text-[#C85C6E]' =>
                            request()->routeIs('gender.index') &&
                            request()->route('gender') == $navGender->slug,
                    ])>
                        {{ $navGender->name }}
                        <svg class="w-3.5 h-3.5 opacity-60 transition-transform duration-200"
                            :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>

                    {{-- Level 1 dropdown — categories --}}
                    <div x-show="open" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1" class="absolute left-0 top-full pt-1 z-50"
                        style="display:none; min-width: 180px;">

                        <div class="bg-white text-[#1C1C1C] rounded-xl shadow-xl py-2 border border-gray-100">
                            @foreach ($navGender->children as $navCategory)
                                {{-- Each category row — hover reveals subcategory flyout --}}
                                <div class="relative" x-data="{ subOpen: false }" @mouseenter="subOpen = true"
                                    @mouseleave="subOpen = false">

                                    <a href="{{ route('gender.category.show', [$navGender, $navCategory], false) }}"
                                        class="flex items-center justify-between px-4 py-2.5 text-sm
                                                                                                                                                                                                                       hover:bg-[#F7F3EE] hover:text-[#C85C6E] transition-colors"
                                        :class="subOpen ? 'bg-[#F7F3EE] text-[#C85C6E]' : ''">
                                        {{ $navCategory->name }}
                                        @if ($navCategory->children->count() > 0)
                                            <svg class="w-3.5 h-3.5 opacity-50 shrink-0 ml-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        @endif
                                    </a>

                                    {{-- Level 2 flyout — subcategories --}}
                                    @if ($navCategory->children->count() > 0)
                                        <div x-show="subOpen" x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="opacity-0 translate-x-1"
                                            x-transition:enter-end="opacity-100 translate-x-0"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="opacity-100 translate-x-0"
                                            x-transition:leave-end="opacity-0 translate-x-1"
                                            class="absolute left-full top-0 pl-1 z-50"
                                            style="display:none; min-width: 160px;">

                                            <div
                                                class="bg-white text-[#1C1C1C] rounded-xl shadow-xl py-2 border border-gray-100">
                                                {{-- "All [category]" shortcut --}}
                                                <a href="{{ route('gender.category.show', [$navGender, $navCategory], false) }}"
                                                    class="block px-4 py-2 text-xs font-medium text-gray-400
                                                                                                                                                                                                                                                                                                                       hover:bg-[#F7F3EE] hover:text-[#C85C6E] transition-colors
                                                                                                                                                                                                                                                                                                                       border-b border-gray-100 mb-1">
                                                    All {{ $navCategory->name }}
                                                </a>
                                                @foreach ($navCategory->children as $navSubcategory)
                                                    <a href="{{ route('gender.subcategory.show', [$navGender, $navCategory, $navSubcategory], false) }}"
                                                        class="block px-4 py-2 text-sm
                                                                                                                                                                                                                                                                                                                                                                                                               hover:bg-[#F7F3EE] hover:text-[#C85C6E] transition-colors">
                                                        {{ $navSubcategory->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

            <a href="/about"
                class="px-4 py-2 text-sm font-light tracking-wide hover:text-[#C85C6E] transition-colors
                      @active('about')
text-[#C85C6E]
@endactive">
                About
            </a>
            <a href="/contact"
                class="px-4 py-2 text-sm font-light tracking-wide hover:text-[#C85C6E] transition-colors
                      @active('contact')
text-[#C85C6E]
@endactive">
                Contact
            </a>
        </div>

        {{-- RIGHT ICONS --}}
        <div class="flex items-center gap-2">
            @hasrole(['admin', 'super_admin'])
                {{-- {{ route('admin.dashboard') }} --}}
                <a href="/admin" class="px-3 py-1.5 text-sm font-light text-gray-300 hover:text-white transition-colors">
                    Admin
                </a>
            @endhasrole
            @auth
                <div class="hidden md:flex items-center gap-2">
                    <a href="{{ route('orders.index') }}"
                        class="px-3 py-1.5 text-sm font-light text-gray-300 hover:text-white transition-colors">
                        Orders
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="px-4 py-1.5 text-sm border border-white/20 rounded-full hover:border-white/60 transition-colors font-light">
                            Sign Out
                        </button>
                    </form>
                </div>
            @else
                <div class="hidden md:flex items-center gap-2">
                    <a href="{{ route('login') }}"
                        class="px-4 py-1.5 text-sm font-light text-gray-300 hover:text-white transition-colors">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}"
                        class="px-4 py-1.5 text-sm bg-[#C85C6E] rounded-full hover:bg-[#b54e60] transition-colors font-medium">
                        Sign Up
                    </a>
                </div>
            @endauth

            <button @click="cartOpen = !cartOpen"
                class="relative p-2.5 hover:bg-white/10 rounded-full transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                @if (isset($cartCount) && $cartCount > 0)
                    <span
                        class="absolute -top-0.5 -right-0.5 bg-[#C85C6E] text-white text-[10px] font-bold
                                                                                                                     rounded-full flex items-center justify-center"
                        style="min-width:18px;min-height:18px;padding:0 4px;">
                        {{ $cartCount }}
                    </span>
                @endif
            </button>

            <button @click="mobileMenuOpen = !mobileMenuOpen"
                class="md:hidden p-2.5 hover:bg-white/10 rounded-full transition-colors">
                <svg x-show="!mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    style="display:none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </nav>

    {{-- MOBILE MENU — accordion: gender → categories → subcategories --}}
    <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        class="md:hidden border-t border-white/10 bg-[#1C1C1C]" style="display:none;">
        <div class="px-6 py-4 space-y-1">
            <a href="/" class="block py-2.5 text-sm font-light hover:text-[#C85C6E] transition-colors">Home</a>

            @foreach ($navGenders as $navGender)
                <div x-data="{ gOpen: false }">
                    {{-- Gender row --}}
                    <button @click="gOpen = !gOpen"
                        class="w-full text-left py-2.5 text-sm font-light hover:text-[#C85C6E] transition-colors
                                                                                                                       flex items-center justify-between">
                        {{ $navGender->name }}
                        <svg class="w-4 h-4 transition-transform" :class="gOpen ? 'rotate-180' : ''" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="gOpen" class="pl-3 border-l border-white/10 ml-1 space-y-0.5" style="display:none;">
                        @foreach ($navGender->children as $navCategory)
                            <div x-data="{ cOpen: false }">
                                {{-- Category row --}}
                                <button @click="cOpen = !cOpen"
                                    class="w-full text-left py-2 text-sm text-gray-300 hover:text-[#C85C6E]
                                                                                                                                                                                                                       transition-colors flex items-center justify-between">
                                    {{ $navCategory->name }}
                                    @if ($navCategory->children->count() > 0)
                                        <svg class="w-3.5 h-3.5 transition-transform"
                                            :class="cOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    @endif
                                </button>

                                {{-- Subcategory list --}}
                                @if ($navCategory->children->count() > 0)
                                    <div x-show="cOpen" class="pl-3 border-l border-white/10 ml-1 pb-1"
                                        style="display:none;">
                                        <a href="{{ route('gender.category.show', [
                                            'gender' => $navGender,
                                            'category' => $navCategory,
                                        ]) }}"
                                            class="block py-1.5 text-xs text-gray-500 hover:text-[#C85C6E] transition-colors">
                                            All {{ $navCategory->name }}
                                        </a>
                                        @foreach ($navCategory->children as $navSubcategory)
                                            <a href="{{ route('gender.subcategory.show', [
                                                'gender' => $navGender,
                                                'category' => $navCategory,
                                                'subcategory' => $navSubcategory,
                                            ]) }}"
                                                class="block py-1.5 text-sm text-gray-400 hover:text-[#C85C6E] transition-colors">
                                                {{ $navSubcategory->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <a href="/about" class="block py-2.5 text-sm font-light hover:text-[#C85C6E] transition-colors">About</a>
            <a href="/contact"
                class="block py-2.5 text-sm font-light hover:text-[#C85C6E] transition-colors">Contact</a>

            <div class="pt-3 border-t border-white/10 flex gap-3">
                @auth
                    <a href="{{ route('orders.index') }}"
                        class="text-sm text-gray-400 hover:text-white transition-colors">Orders</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-sm text-gray-400 hover:text-white transition-colors">Sign
                            Out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Sign
                        In</a>
                    <a href="{{ route('register') }}"
                        class="text-sm bg-[#C85C6E] text-white px-4 py-1.5 rounded-full hover:bg-[#b54e60] transition-colors">Sign
                        Up</a>
                @endauth
            </div>
        </div>
    </div>
</header>
