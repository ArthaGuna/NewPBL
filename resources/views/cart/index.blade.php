<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold">Keranjang Belanja</h2>

        @if (session('error'))
            <div class="bg-red-500 text-white p-4 rounded mt-4">
                {{ session('error') }}
            </div>
        @endif

        @if (isset($cart) && $cart->items->isNotEmpty())
            <table class="w-full table-auto border-collapse mt-4">
                <thead>
                    <tr>
                        <th class="border px-4 py-2">Produk</th>
                        <th class="border px-4 py-2">Jumlah</th>
                        <th class="border px-4 py-2">Harga</th>
                        <th class="border px-4 py-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cart->items as $item)
                        <tr>
                            <td class="border px-4 py-2">{{ $item->product->name }}</td>
                            <td class="border px-4 py-2">{{ $item->quantity }}</td>
                            <td class="border px-4 py-2">Rp {{ number_format($item->price) }}</td>
                            <td class="border px-4 py-2">Rp {{ number_format($item->quantity * $item->price) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                <h3 class="font-semibold">Subtotal: Rp {{ number_format($subTotal) }}</h3>
            </div>

            <div class="mt-4">
                <a href="{{ route('cart.checkout') }}" class="bg-blue-500 text-white px-6 py-2 rounded">Checkout</a>
            </div>
        @else
            <p class="text-gray-500 mt-4">Keranjang Anda kosong.</p>
        @endif
    </div>
</x-app-layout>
