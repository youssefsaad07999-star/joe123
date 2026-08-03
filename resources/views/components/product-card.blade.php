@props(['product', 'gender', 'category', 'subcategory'])

<div class="group relative bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500">
    <a href="{{ route('gender.product.show', [
        'gender' => $gender->slug,
        'category' => $category->slug,
        'subcategory' => $subcategory->slug,
        'product' => $product->slug,
    ]) }}"
        class="block">
        {{-- Image --}}
        <div class="relative aspect-[3/4] overflow-hidden bg-gray-100">
            @php
                // Just grab the first image object safely. Do NOT read ->image_path here!
                $image = $product->primaryImage?->image_path;
            @endphp

            {{-- Check if the image object actually exists before attempting to read its properties --}}
            @if ($image)
                <img src="{{ asset('storage/' . $image) }}" alt="{{ $product->name }}"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
            @else
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                    <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            @endif

            {{-- Badge --}}
            @if (isset($product->is_new) && $product->is_new)
                <span
                    class="absolute top-3 left-3 bg-[#1C1C1C] text-white text-[10px] font-semibold tracking-widest uppercase px-2.5 py-1 rounded-full">
                    New
                </span>
            @endif
            @if (isset($product->discount) && $product->discount > 0)
                <span
                    class="absolute top-3 left-3 bg-[#C85C6E] text-white text-[10px] font-semibold tracking-widest uppercase px-2.5 py-1 rounded-full">
                    -{{ $product->discount }}%
                </span>
            @endif

            {{-- Quick Add Overlay --}}
            {{-- <div
            class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end">
            <form action="" method="POST" class="w-full p-3">
                {{ route('cart.store') }}
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="w-full bg-white text-[#1C1C1C] text-sm font-medium py-2.5 rounded-xl
                               hover:bg-[#C85C6E] hover:text-white transition-colors duration-200">
                    Quick Add
                </button>
            </form>
        </div> --}}
        </div>

        {{-- Info --}}
        <div class="p-4">

            <h3
                class="font-medium text-[#1C1C1C] text-sm leading-snug group-hover:text-[#C85C6E] transition-colors truncate">
                {{ $product->name }}
            </h3>
            <div class="flex items-center gap-2 mt-1.5">
                @if (isset($product->original_price) && $product->original_price > $product->price)
                    <span
                        class="text-gray-400 text-xs line-through">${{ number_format($product->base_price, 2) }}</span>
                @endif
                <span class="font-semibold text-[#1C1C1C] text-sm">${{ number_format($product->base_price, 2) }}</span>
            </div>
    </a>
</div>
</div>
