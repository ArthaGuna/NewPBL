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

                {{-- Pilihan Ukuran dengan Tombol --}}
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

                {{-- Ukuran dan Harga Dinamis --}}
                <div class="text-lg">
                    <strong>Ukuran:</strong> <span id="size-display">{{ $product->sizes->first()->size ?? '-' }}</span> <br>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex flex-wrap gap-4 items-center">
                    {{-- Tombol Quantity --}}
                    <div class="flex items-center">
                        <button id="decrease-quantity" class="bg-gray-200 px-2 py-1 text-lg font-semibold text-gray-700 hover:bg-gray-300 rounded-l-md">-</button>
                        <input type="number" name="quantity" id="quantity-input" value="1" min="1" class="quantity-input border w-16 bg-gray-200 text-center py-1 text-sm font-medium" style="height: 36px; border: none;">
                        <button id="increase-quantity" class="bg-gray-200 px-2 py-1 text-lg font-semibold text-gray-700 hover:bg-gray-300 rounded-r-md">+</button>
                    </div>

                    {{-- Tombol-Tombol di Samping --}}
                    <div class="flex gap-4">
                        {{-- Tombol Tambah ke Keranjang --}}
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <label for="quantity">Jumlah:</label>
                            <input type="number" name="quantity" value="1" min="1">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                                Tambah ke Keranjang
                            </button>
                        </form>                   

                        {{-- Tombol Bayar --}}
                        <button class="border bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Bayar Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Notifikasi dan Badge Keranjang --}}
    <div id="cart-badge" class="fixed bottom-10 right-10 bg-blue-500 text-white rounded-full p-3 shadow-lg flex items-center justify-center">
        <span id="cart-count" class="font-bold">0</span>
    </div>

    {{-- Script untuk Interaksi Dinamis --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Handle Add to Cart with AJAX
        $('#add-to-cart-form').on('submit', function(e) {
    e.preventDefault();

    var quantity = $('#quantity-input').val();
    var product_id = $('input[name="product_id"]').val();

    $.ajax({
        url: '{{ route('cart.add') }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            product_id: product_id,
            quantity: quantity
        },
        success: function(response) {
            if (response.success) {
                // Update jumlah keranjang
                $('#cart-count').text(response.cartItemCount);
                // Tampilkan notifikasi
                alert(response.message);
            }
        },
        error: function(xhr, status, error) {
            alert('Terjadi kesalahan. Silakan coba lagi.');
        }
    });
});


        // Script untuk mengubah gambar utama berdasarkan thumbnail
        const thumbnails = document.querySelectorAll('.thumbnail');
        const mainImage = document.getElementById('main-image');

        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', () => {
                mainImage.src = thumbnail.src; // Ganti gambar utama dengan thumbnail
            });
        });

        // Script untuk mengubah ukuran dan harga dinamis
        const sizeButtons = document.querySelectorAll('.size-button');
        sizeButtons.forEach(button => {
            button.addEventListener('click', function () {
                // Reset semua tombol ukuran
                sizeButtons.forEach(btn => btn.classList.remove('bg-blue-500', 'text-white'));
                sizeButtons.forEach(btn => btn.classList.add('bg-gray-100', 'text-gray-700'));
                
                // Set tombol yang dipilih
                this.classList.remove('bg-gray-100', 'text-gray-700');
                this.classList.add('bg-gray-300', 'text-gray-800');

                // Update tampilan ukuran dan harga
                const selectedSize = this.getAttribute('data-size');
                const selectedPrice = this.getAttribute('data-price');
                document.getElementById('size-display').textContent = selectedSize;
                document.getElementById('price-display').textContent = new Intl.NumberFormat('id-ID').format(selectedPrice);
            });
        });

        // Script untuk mengubah jumlah quantity
        const quantityInput = document.getElementById('quantity-input');
        const increaseBtn = document.getElementById('increase-quantity');
        const decreaseBtn = document.getElementById('decrease-quantity');

        increaseBtn.addEventListener('click', () => {
            quantityInput.value = parseInt(quantityInput.value) + 1;
        });

        decreaseBtn.addEventListener('click', () => {
            if (parseInt(quantityInput.value) > 1) {
                quantityInput.value = parseInt(quantityInput.value) - 1;
            }
        });
    </script>
</x-app-layout>
