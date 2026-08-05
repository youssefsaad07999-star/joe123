@props(['product'])


<div
    class="group relative bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-shadow duration-500">

    {{-- Wishlist toggle — sits above the link, own click target --}}
    <button type="button" x-data="{ saved: {{ $product->is_wishlisted ?? 'false' }} }" @click="saved = !saved" :aria-pressed="saved"
        aria-label="Toggle wishlist"
        class="absolute top-3 right-3 z-10 w-9 h-9 rounded-full bg-white/90 backdrop-blur flex items-center justify-center shadow-sm hover:scale-110 transition-transform duration-200">
        <svg :class="saved ? 'fill-[#C85C6E] stroke-[#C85C6E]' : 'fill-none stroke-[#1C1C1C]'"
            class="w-4.5 h-4.5 transition-colors" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 20.25c-.18 0-.36-.06-.51-.17C7.4 17.1 3 13.5 3 9.3 3 6.4 5.3 4 8.2 4c1.6 0 3.1.8 3.8 2.1C12.7 4.8 14.2 4 15.8 4 18.7 4 21 6.4 21 9.3c0 4.2-4.4 7.8-8.49 10.78-.15.11-.33.17-.51.17Z" />
        </svg>
    </button>

    <a href="{{ route('product.show', $product) }}" class="block">

        {{-- Image --}}
        <div class="relative aspect-3/4 overflow-hidden bg-gray-100">
            @php
                $image = $product->primaryImage?->image_path;
            @endphp

            @if ($image)
                <img src="{{ asset('storage/' . $image) }}" alt="{{ $product->name }}" loading="lazy" width="600"
                    height="800"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
            @else
                <div class="w-full h-full flex items-center justify-center bg-linear-to-br from-gray-100 to-gray-200">
                    <svg class="  text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            @endif

            {{-- Badges — new + discount can coexist, stack them --}}
            <div class="absolute top-3 left-3 flex flex-col gap-1.5">
                @if ($product->is_new ?? false)
                    <span
                        class="bg-[#1C1C1C] text-white text-[10px] font-semibold tracking-widest uppercase px-2.5 py-1 rounded-full w-fit">
                        New
                    </span>
                @endif
                @if (($product->discount ?? 0) > 0)
                    <span
                        class="bg-[#C85C6E] text-white text-[10px] font-semibold tracking-widest uppercase px-2.5 py-1 rounded-full w-fit">
                        -{{ $product->discount }}%
                    </span>
                @endif
            </div>
        </div>

        {{-- Info --}}
        <div class="p-4">
            <h3
                class="font-medium text-[#1C1C1C] text-sm leading-snug group-hover:text-[#C85C6E] transition-colors truncate">
                {{ $product->name }}
            </h3>

            <div class="flex items-center gap-2 mt-1.5">
                @if (($product->sale_price ?? null) && $product->sale_price < $product->base_price)
                    <span
                        class="text-gray-400 text-xs line-through">${{ number_format($product->base_price, 2) }}</span>
                    <span
                        class="font-semibold text-[#C85C6E] text-sm">${{ number_format($product->sale_price, 2) }}</span>
                @else
                    <span
                        class="font-semibold text-[#1C1C1C] text-sm">${{ number_format($product->base_price, 2) }}</span>
                @endif
            </div>
        </div>
    </a>

    {{-- Quick Add — outside the <a>, its own form, actually wired to the route --}}
    {{-- <div
        class="absolute inset-x-0 bottom-0 translate-y-full group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-300 p-3 pt-8 bg-linear-to-t from-white via-white/95 to-transparent">
        <form action="{{ route('cart.store') }}" method="POST">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="quantity" value="1">
            <button type="submit"
                class="w-full bg-[#1C1C1C] text-white text-sm font-medium py-2.5 rounded-xl
                       hover:bg-[#C85C6E] transition-colors duration-200">
                Quick Add
            </button>
        </form>
    </div> --}}
</div>
