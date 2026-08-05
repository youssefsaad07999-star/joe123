<x-layout>
    <div class="max-w-7xl mx-auto px-6 py-6 md:py-12">

        {{-- Breadcrumb --}}
        <div class="opacity-80 transition-opacity hover:opacity-100">
            <x-breadcrumb :items="[
                ['label' => $gender->name, 'url' => route('gender.index', ['gender' => $gender])],
                [
                    'label' => $category->name,
                    'url' => route('gender.category.show', ['gender' => $gender->slug, 'category' => $category->slug]),
                ],
                ['label' => $subcategory->name, 'url' => null],
            ]" />
        </div>

        {{-- Header Section --}}
        <header
            class="mt-8 md:mt-12 mb-10 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 border-b border-gray-100 pb-8">
            <div class="space-y-2">
                <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.2em] uppercase">
                    {{ $gender->name }} · {{ $category->name }}
                </p>
                <h1
                    class="font-['Cormorant_Garamond'] text-5xl md:text-6xl font-light tracking-tight text-gray-900 capitalize">
                    {{ $subcategory->name }}
                </h1>
            </div>

            {{-- Other Subcategories Scrollable Strip --}}
            <nav class="flex items-center gap-2 overflow-x-auto whitespace-nowrap -mx-6 px-6 lg:mx-0 lg:px-0 pb-2 lg:pb-0 scrollbar-none"
                aria-label="Subcategories Navigation">
                @foreach ($subcategories as $sub)
                    <a href="{{ route('gender.subcategory.show', [
                        'gender' => $gender->slug,
                        'category' => $category->slug,
                        'subcategory' => $sub->slug,
                    ]) }}"
                        class="px-4 py-2 rounded-full text-xs font-medium tracking-wide border transition-all duration-300 shrink-0 select-none
                        {{ $sub->id === $subcategory->id
                            ? 'bg-[#1C1C1C] text-white border-[#1C1C1C] shadow-sm'
                            : 'border-gray-200 text-gray-600 bg-white hover:border-[#C85C6E] hover:text-[#C85C6E]' }}">
                        {{ $sub->name }}
                    </a>
                @endforeach
            </nav>
        </header>

        {{-- Sort Utility Toolbar --}}
        <div class="flex items-center justify-between mb-8">
            <p class="text-gray-400 text-xs md:text-sm font-light tracking-wide">
                Showing <span
                    class="font-normal text-gray-700">{{ $products instanceof \Illuminate\Pagination\LengthAwarePaginator ? $products->total() : count($products) }}</span>
                results
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
                    @foreach (['newest' => 'Newest First', 'price_asc' => 'Price: Low to High', 'price_desc' => 'Price: High to Low', 'popular' => 'Most Popular'] as $val => $label)
                        <a href="{{ request()->fullUrlWithQuery(['sort' => $val]) }}"
                            class="block px-4 py-2.5 text-xs md:text-sm transition-colors hover:bg-gray-50 {{ request('sort') === $val ? 'text-[#C85C6E] font-semibold bg-rose-50/30' : 'text-gray-600' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Products Grid System --}}
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
        <p class="text-gray-400 font-light text-base">No products available in this section yet.</p>
        <a href="{{ route('gender.category.show', [
            'gender' => $gender->slug,
            'category' => $category->slug,
        ]) }}"
            class="inline-flex items-center gap-1.5 mt-4 text-xs font-medium uppercase tracking-wider text-[#C85C6E] hover:text-black transition-colors">
            <span>← Back to {{ $category->name }}</span>
        </a>
    </div>
    @endforelse

    <x-paginator :paginator="$products" />

    </div>
</x-layout>
