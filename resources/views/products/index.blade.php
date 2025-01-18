<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        {{-- Products Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($products as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <a href="{{ route('products.show', $product) }}">
                    <div class="relative h-25">
                        @if($product->photos->count() > 0)
                            {{-- Ambil foto pertama dari relasi photos --}}
                            <img src="{{ Storage::url($product->photos->first()->photo) }}" 
                            alt="{{ $product->name }}" 
                            class="w-full h-full object-cover">
                        @else
                            {{-- Placeholder jika foto tidak tersedia --}}
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-400">No image</span>
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-2">{{ $product->name }}</h3>
                        <p class="text-gray-600 text-sm mb-2">{{ $product->category->name }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-blue-600 font-bold">
                                @if($product->sizes->count() > 0)
                                    Rp {{ number_format($product->sizes->min('price')) }} 
                                    - 
                                    Rp {{ number_format($product->sizes->max('price')) }}
                                @else
                                    Price not set
                                @endif
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $products->links() }}
        </div>
    </div>
</x-app-layout>
