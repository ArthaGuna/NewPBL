<!-- resources/views/cart/checkout.blade.php -->

<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold">Checkout</h2>

        <form action="{{ route('cart.processCheckout') }}" method="POST">
            @csrf
            <div class="mt-4">
                <label for="address" class="block">Alamat Pengiriman</label>
                <textarea name="address" id="address" class="w-full border p-2 mt-2" required>{{ old('address') }}</textarea>
            </div>

            <div class="mt-4">
                <label for="courier_id" class="block">Pilih Kurir</label>
                <select name="courier_id" id="courier_id" class="w-full border p-2 mt-2" required>
                    @foreach ($couriers as $courier)
                        <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mt-4">
                <label for="promo_code" class="block">Kode Promo (Opsional)</label>
                <input type="text" name="promo_code" id="promo_code" class="w-full border p-2 mt-2" value="{{ old('promo_code') }}">
            </div>

            <div class="mt-4">
                <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded">Proses Checkout</button>
            </div>
        </form>
    </div>
</x-app-layout>
