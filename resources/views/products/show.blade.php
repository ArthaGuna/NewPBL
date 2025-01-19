<x-app-layout>
    <div class="container mx-auto px-4 py-8 mt-6 flex justify-center items-center min-h-screen">
        <div class="flex flex-col md:flex-row items-start gap-8">
            {{-- Bagian Gambar --}}
            <div class="flex-1 flex flex-col items-center mb-6 md:mb-0">
                @if($product->photos->count() > 0)
                    {{-- Gambar Utama --}}
                    <div class="mb-4 w-full max-w-sm" style="height: 30rem;">
                        <img id="main-image" src="{{ Storage::url($product->photos->first()->photo) }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-full object-cover rounded-lg border border-gray-300 shadow-md">
                    </div>

                    {{-- Thumbnail Pilihan Gambar --}}
                    <div class="flex gap-2 overflow-x-auto">
                        @foreach($product->photos as $photo)
                            <img src="{{ Storage::url($photo->photo) }}" 
                                 alt="{{ $product->name }}" 
                                 class="thumbnail w-16 h-16 object-cover rounded-lg border border-gray-300 cursor-pointer hover:border-blue-500">
                        @endforeach
                    </div>
                @else
                    {{-- Placeholder jika foto tidak tersedia --}}
                    <div class="w-full h-full bg-gray-200 flex items-center justify-center rounded-lg">
                        <span class="text-gray-400">No image</span>
                    </div>
                @endif
            </div>

            {{-- Bagian Detail Produk --}}
            <div class="flex-1 space-y-6 ml-8 md:ml-16">
                <h1 class="text-2xl md:text-3xl font-bold">{{ $product->name }}</h1>
                <div class="mt-1 text-xl">
                    <strong>Harga:</strong> Rp <span id="price-display">{{ number_format($product->sizes->first()->price ?? 0) }}</span>
                </div>
                <p class="text-gray-600 leading-relaxed">Deskripsi: {{ $product->about }}</p>

                {{-- Pilihan Ukuran --}}
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Pilih Ukuran:</label>
                    <div class="flex gap-2 flex-wrap">
                        @foreach($product->sizes as $size)
                            <button 
                                class="size-button border bg-gray-100 text-gray-700 px-4 py-2 rounded hover:bg-gray-200 focus:outline-none focus:ring focus:ring-gray-300 transition" 
                                data-price="{{ $size->price }}" 
                                data-size="{{ $size->size }}">
                                {{ $size->size }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex flex-col gap-4 items-start">
                    {{-- Tombol Quantity --}}
                    <div class="flex items-center">
                        <button id="decrease-quantity" class="bg-gray-200 px-2 py-1 text-lg font-semibold text-gray-700 hover:bg-gray-300 rounded-l-md">-</button>
                        <span id="quantity-display" 
                            class="quantity-input border w-16 h-9 bg-gray-200 text-center py-1 text-sm font-medium flex items-center justify-center">
                            1
                        </span>
                        <button id="increase-quantity" class="bg-gray-200 px-2 py-1 text-lg font-semibold text-gray-700 hover:bg-gray-300 rounded-r-md">+</button>
                    </div>

                    {{-- Tombol-Tombol di Bawah --}}
                    <div class="flex gap-4 w-full">
                        {{-- Tombol Tambah ke Keranjang --}}
                        <form id="addToCartForm" class="w-full">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" id="selected-size" name="size">
                            <input type="hidden" id="selected-price" name="price" value="{{ $product->sizes->first()->price }}">
                            <input type="hidden" id="selected-quantity" name="quantity" value="1">

                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-cart-plus"></i> <!-- Ikon Keranjang -->
                                Tambah ke Keranjang
                            </button>
                        </form>

                        {{-- Tombol Bayar Sekarang
                        <button class="w-full bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                            Bayar Sekarang
                        </button> --}}
                    </div>
                </div>

                {{-- Loading Indicator --}}
                <div id="loading" class="hidden text-gray-600">Sedang memproses...</div>
            </div>
        </div>
    </div>

    {{-- Script untuk Interaksi Dinamis --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sizeButtons = document.querySelectorAll('.size-button');
            const priceDisplay = document.getElementById('price-display');
            const sizeInput = document.getElementById('selected-size');
            const priceInput = document.getElementById('selected-price');
            const quantityDisplay = document.getElementById('quantity-display');
            const quantityInput = document.getElementById('selected-quantity');
            const thumbnails = document.querySelectorAll('.thumbnail');
            const mainImage = document.getElementById('main-image');

            // Pilihan ukuran
            sizeButtons.forEach(button => {
                button.addEventListener('click', function () {
                    sizeButtons.forEach(btn => btn.classList.remove('bg-gray-200', 'text-gray-700'));
                    this.classList.add('bg-gray-200', 'text-gray-700');

                    const selectedSize = this.getAttribute('data-size');
                    const selectedPrice = this.getAttribute('data-price');

                    priceDisplay.textContent = new Intl.NumberFormat('id-ID').format(selectedPrice);
                    sizeInput.value = selectedSize;
                    priceInput.value = selectedPrice;
                });
            });

            // Tambah dan kurangi quantity
            document.getElementById('increase-quantity').addEventListener('click', function () {
                let quantity = parseInt(quantityDisplay.textContent);
                quantity++;
                quantityDisplay.textContent = quantity;
                quantityInput.value = quantity;
            });

            document.getElementById('decrease-quantity').addEventListener('click', function () {
                let quantity = parseInt(quantityDisplay.textContent);
                if (quantity > 1) {
                    quantity--;
                    quantityDisplay.textContent = quantity;
                    quantityInput.value = quantity;
                }
            });

            // Pilihan Thumbnail
            thumbnails.forEach(thumbnail => {
                thumbnail.addEventListener('click', function () {
                    thumbnails.forEach(img => img.classList.remove('active'));
                    this.classList.add('active');
                    mainImage.src = this.src;
                });
            });

            // Mengirimkan data ke server
            document.getElementById('addToCartForm').addEventListener('submit', async function (e) {
                e.preventDefault();
                const addToCartButton = this.querySelector('button');
                const loading = document.getElementById('loading');

                // Validasi ukuran
                if (!sizeInput.value) {
                    alert('Silakan pilih ukuran terlebih dahulu');
                    return;
                }

                // Tampilkan loading dan disable tombol
                addToCartButton.disabled = true;
                loading.classList.remove('hidden');

                try {
                    const response = await fetch('/cart', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            product_id: this.querySelector('input[name="product_id"]').value,
                            size: sizeInput.value,
                            quantity: quantityInput.value,
                            price: priceInput.value
                        })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        alert(data.message);
                    } else {
                        throw new Error(data.message || 'Terjadi kesalahan');
                    }
                } catch (error) {
                    alert(error.message || 'Gagal menambahkan ke keranjang');
                } finally {
                    loading.classList.add('hidden');
                    addToCartButton.disabled = false;
                }
            });
        });
    </script>
</x-app-layout>
