<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Keranjang Belanja</h1>

        @if($cartItems->count() > 0)
            <div class="flex flex-col md:flex-row gap-8">
                <!-- Daftar Item -->
                <div class="md:w-2/3">
                    @foreach($cartItems as $item)
                        <div class="flex items-center border-b py-4" id="cart-item-{{ $item->id }}">
                            <!-- Gambar Produk -->
                            <div class="w-24 h-24">
                                @if($item->product->photos->count() > 0)
                                    <img src="{{ Storage::url($item->product->photos->first()->photo) }}"
                                         alt="{{ $item->product->name }}"
                                         class="w-full h-full object-cover rounded">
                                @endif
                            </div>

                            <!-- Detail Produk -->
                            <div class="flex-1 ml-4">
                                <h3 class="font-semibold">{{ $item->product->name }}</h3>
                                <p class="text-gray-600">Ukuran: {{ $item->size }}</p>
                                <p class="text-gray-600">Harga: Rp {{ number_format($item->price) }}</p>
                                
                                <!-- Quantity Controls -->
                                <div class="flex items-center mt-2">
                                    <button 
                                        class="quantity-decrease bg-gray-200 px-2 py-1 rounded-l"
                                        data-item-id="{{ $item->id }}">-</button>
                                    <input type="number" 
                                        class="quantity-input w-16 text-center border-y border-gray-200" 
                                        value="{{ $item->quantity }}"
                                        min="1"
                                        data-item-id="{{ $item->id }}">
                                    <button 
                                        class="quantity-increase bg-gray-200 px-2 py-1 rounded-r"
                                        data-item-id="{{ $item->id }}">+</button>
                                </div>
                            </div>

                            <!-- Subtotal dan Hapus -->
                            <div class="text-right ml-4">
                                <p class="font-semibold">Rp {{ number_format($item->subtotal) }}</p>
                                <button 
                                    class="delete-item text-red-500 mt-2"
                                    data-item-id="{{ $item->id }}">Hapus</button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Ringkasan -->
                <div class="md:w-1/3">
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h2 class="text-xl font-semibold mb-4">Ringkasan Belanja</h2>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span>Total Item:</span>
                                <span>{{ $cartItems->sum('quantity') }}</span>
                            </div>
                            <div class="flex justify-between font-semibold">
                                <span>Total Harga:</span>
                                <span>Rp {{ number_format($cartItems->sum('subtotal')) }}</span>
                            </div>
                        </div>
                        <button class="w-full bg-blue-500 text-white py-2 px-4 rounded mt-4 hover:bg-blue-600">
                            Lanjut ke Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-600">Keranjang belanja Anda kosong</p>
                <a href="{{ route('products.index') }}" class="text-blue-500 hover:underline mt-2 inline-block">
                    Mulai Belanja
                </a>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Update quantity
            function updateQuantity(itemId, newQuantity) {
                fetch(`/cart/${itemId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ quantity: newQuantity })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.reload(); // Refresh page
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => alert('Terjadi kesalahan'));
            }

            // Quantity controls
            document.querySelectorAll('.quantity-decrease').forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.dataset.itemId;
                    const input = document.querySelector(`.quantity-input[data-item-id="${itemId}"]`);
                    if (input.value > 1) {
                        input.value = parseInt(input.value) - 1;
                        updateQuantity(itemId, input.value);
                    }
                });
            });

            document.querySelectorAll('.quantity-increase').forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.dataset.itemId;
                    const input = document.querySelector(`.quantity-input[data-item-id="${itemId}"]`);
                    input.value = parseInt(input.value) + 1;
                    updateQuantity(itemId, input.value);
                });
            });

            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('change', function() {
                    const itemId = this.dataset.itemId;
                    if (this.value < 1) this.value = 1;
                    updateQuantity(itemId, this.value);
                });
            });

            // Delete item
            document.querySelectorAll('.delete-item').forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.dataset.itemId;
                    if (confirm('Apakah Anda yakin ingin menghapus item ini?')) {
                        fetch(`/cart/${itemId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log(data);  // Cek data yang diterima dari server
                            if (data.status === 'success') {
                                window.location.reload(); // Refresh page
                            } else {
                                alert(data.message);
                            }
                        })

                        .catch(error => alert('Terjadi kesalahan'));
                    }
                });
            });
        });
    </script>
</x-app-layout>
