<x-layout>
    <div class="max-w-7xl mx-auto px-6 py-6 md:py-12">

        {{-- Breadcrumb --}}
        <div class="opacity-80 transition-opacity hover:opacity-100">
            <x-breadcrumb :items="[
                ['label' => ucfirst($gender->slug), 'url' => route('gender.index', ['gender' => $gender->slug])],
                ['label' => $category->name, 'url' => null],
            ]" />
        </div>

        {{-- Header --}}
        <header class="mt-8 md:mt-12 mb-8 md:mb-12">
            <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.25em] uppercase mb-2">
                {{ $gender->name }}
            </p>
            <h1
                class="font-['Cormorant_Garamond'] text-5xl md:text-7xl font-light tracking-tight text-gray-900 capitalize">
                {{ $category->name }}
            </h1>
        </header>

        {{-- Subcategory Scrollable Pill Track --}}
        <nav class="flex items-center gap-2.5 overflow-x-auto whitespace-nowrap -mx-6 px-6 md:mx-0 md:px-0 pb-6 border-b border-gray-200/60 scrollbar-none"
            aria-label="Subcategory Filters">
            <a href="{{ route('gender.category.show', ['gender' => $gender->slug, 'category' => $category->slug]) }}"
                class="px-5 py-2.5 rounded-full text-xs font-medium uppercase tracking-wider border transition-all duration-300 shrink-0 select-none
                    {{ !request('sub')
                        ? 'bg-[#1C1C1C] text-white border-[#1C1C1C] shadow-sm'
                        : 'border-gray-200 text-gray-600 bg-white hover:border-[#C85C6E] hover:text-[#C85C6E]' }}">
                All {{ $category->name }}
            </a>

            @foreach ($subcategories as $subcategory)
                <a href="{{ route('gender.subcategory.show', ['gender' => $gender->slug, 'category' => $category->slug, 'subcategory' => $subcategory->slug]) }}"
                    class="px-5 py-2.5 rounded-full text-xs font-medium uppercase tracking-wider border transition-all duration-300 shrink-0 select-none
                        {{ request()->routeIs('*subcategory*') && request()->route('subcategory')?->id === $subcategory->id
                            ? 'bg-[#C85C6E] text-white border-[#C85C6E] shadow-sm'
                            : 'border-gray-200 text-gray-600 bg-white hover:border-[#C85C6E] hover:text-[#C85C6E]' }}">
                    {{ $subcategory->name }}
                </a>
            @endforeach
        </nav>

        {{-- Interactive Filter & Sort Tool Bar --}}
        <div class="flex items-center justify-between my-8">
            <p class="text-gray-400 text-xs md:text-sm font-light tracking-wide">
                Showing <span class="font-normal text-gray-700">{{ count($products) }}</span> products
            </p>

            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="flex items-center gap-2 text-xs md:text-sm font-medium border border-gray-200 bg-white rounded-full px-5 py-2.5 text-gray-700 hover:border-gray-400 hover:text-gray-900 transition-all cursor-pointer shadow-sm">
                    <span>Sort By</span>
                    <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200"
                        :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open" x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95" @click.outside="open = false"
                    class="absolute right-0 top-full mt-2 w-52 bg-white rounded-2xl shadow-xl border border-gray-100 py-2.5 z-20 origin-top-right"
                    style="display:none;">
                    @foreach (['newest' => 'Newest First', 'price_asc' => 'Price: Low to High', 'price_desc' => 'Price: High to Low'] as $val => $label)
                        <a href="{{ request()->fullUrlWithQuery(['sort' => $val]) }}"
                            class="block px-4 py-2.5 text-xs md:text-sm transition-colors hover:bg-gray-50 {{ request('sort') === $val ? 'text-[#C85C6E] font-semibold bg-rose-50/30' : 'text-gray-600' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Products Grid --}}
        @forelse($products as $product)
            @if ($loop->first)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-8 md:gap-x-6 md:gap-y-10">
            @endif

            <div class="transition-transform duration-300 hover:-translate-y-1">
                <x-product-card :product="$product" />
            </div>

            @if ($loop->last)
    </div>
    @endif
@empty
    <div class="text-center py-24 bg-white/40 border border-dashed border-gray-200/80 rounded-2xl px-4">
        <svg class="w-12 h-12 mx-auto text-gray-300 mb-4 font-light" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
        <p class="text-gray-500 font-medium text-sm">No products in this category yet.</p>
        <a href="{{ route('gender.index', ['gender' => $gender->slug]) }}"
            class="inline-flex items-center gap-1.5 mt-4 text-xs font-medium uppercase tracking-wider text-[#C85C6E] hover:text-black transition-colors">
            <span>Browse all {{ $gender->name }}'s items →</span>
        </a>
    </div>
    @endforelse

    <x-paginator :paginator="$products" />

    </div>
</x-layout>
