<x-layout>
    <div class="max-w-7xl mx-auto px-6 py-6 md:py-12">

        {{-- Breadcrumb --}}
        <div class="opacity-80 transition-opacity hover:opacity-100">
            <x-breadcrumb :items="[['label' => ucfirst($gender->name), 'url' => null]]" />
        </div>

        {{-- Header Section --}}
        <header class="mt-8 md:mt-14 mb-8">
            <p class="text-[#C85C6E] text-xs font-semibold tracking-[0.25em] uppercase mb-2 md:mb-3">
                Collection
            </p>
            <h1
                class="font-['Cormorant_Garamond'] text-5xl md:text-7xl font-light tracking-tight text-gray-900 capitalize">
                {{ $gender->name }}
            </h1>
        </header>

        {{-- Category Horizontal Navigation Scroller --}}
        <nav class="flex items-stretch border-t border-b border-gray-200/50 -mx-6 overflow-x-auto scrollbar-none md:mx-0"
            aria-label="Collection Categories">
            @foreach ($gender->children as $category)
                <a href="{{ route('gender.category.show', [
                    'gender' => $gender->slug,
                    'category' => $category->slug,
                ]) }}"
                    class="group flex items-center justify-between gap-6 py-3.5 px-5 shrink-0 border-r border-gray-100 min-w-[140px] relative overflow-hidden transition-all duration-300 hover:bg-rose-50/10 hover:shadow-[inset_0_-2.5px_0_0_#C85C6E]">

                    {{-- Label & Indicator Group --}}
                    <div class="flex items-center gap-2.5">
                        <!-- Micro Geometric Indicator -->
                        <span
                            class="w-1 h-1 rounded-full bg-gray-300 transition-all duration-300 group-hover:bg-[#C85C6E] group-hover:scale-125"></span>

                        <!-- Clean Architectural Typography -->
                        <span
                            class="font-['Cormorant_Garamond'] text-lg font-medium text-gray-700 transition-colors duration-300 group-hover:text-[#C85C6E]">
                            {{ str($category->name)->title() }}
                        </span>
                    </div>

                    {{-- Context Entry Hint (Slides out elegantly on hover) --}}
                    <span
                        class="text-xs text-gray-300 font-light opacity-0 -translate-x-2 transition-all duration-300 group-hover:opacity-100 group-hover:translate-x-0 group-hover:text-[#C85C6E]">
                        →
                    </span>
                </a>
            @endforeach
        </nav>

        {{-- All Products Grid Section --}}
        <section class="mt-12 md:mt-16">
            <div
                class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-2 mb-8 border-b border-gray-100 pb-4">
                <h2 class="font-['Cormorant_Garamond'] text-3xl md:text-4xl font-light text-gray-900">
                    All {{ ucfirst($gender->name) }}'s Products
                </h2>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-8 md:gap-x-6 md:gap-y-10">
                @forelse($products as $product)
                    <div class="transition-transform duration-300 hover:-translate-y-1">
                        <x-product-card :product="$product" />
                    </div>
                @empty
                    <div
                        class="col-span-full text-center py-24 bg-white/40 rounded-2xl border border-dashed border-gray-200/80 px-4">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-4 font-light" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="text-gray-500 font-medium text-sm">No products found.</p>
                        <p class="text-gray-400 font-light text-xs mt-1">We are updating our stock. Please check back
                            soon!</p>
                    </div>
                @endforelse
            </div>


            <x-paginator :paginator="$products" />
        </section>

    </div>
</x-layout>
