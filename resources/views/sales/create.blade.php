<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transaksi Penjualan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if (session('error'))
                        <div class="mb-4 font-medium text-sm text-red-600">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('sales.store') }}" method="POST" id="sale-form">
                        @csrf
                        
                        <div class="mt-4 mb-6">
                             <label for="product_select" class="block font-medium text-sm text-gray-700">Cari & Tambah Produk:</label>
                             
                             <select id="product_select" class="mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                                <option value="">-- Cari & Pilih Produk --</option>
                                @foreach($products as $product)
                                    <option 
                                        value="{{ $product->id }}" 
                                        data-price="{{ $product->price }}" 
                                        data-name="{{ $product->name }}" 
                                        data-stock="{{ $product->stock }}">
                                        {{ $product->name }} (SKU: {{ $product->sku }}) - Stok: {{ $product->stock }}
                                    </option>
                                @endforeach
                             </select>
                             <p class="text-xs text-red-500 mt-1">Stok produk akan berkurang setelah transaksi berhasil!</p>
                        </div>
                        
                        <h3 class="text-lg font-semibold mb-4 border-b pb-2">Daftar Item Penjualan</h3>
                        <div class="overflow-x-auto border rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Satuan</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Kuantitas</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-body" class="bg-white divide-y divide-gray-200">
                                    <tr id="empty-cart-row">
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada item ditambahkan.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-8 flex justify-end">
                            <div class="w-full md:w-1/2 border p-6 rounded-lg bg-gray-50">
                                
                                <div class="flex justify-between items-center mb-4">
                                    <label class="font-semibold text-lg">TOTAL TAGIHAN:</label>
                                    <p id="grand-total-display" class="text-2xl font-bold text-indigo-600">Rp 0</p>
                                    <input type="hidden" name="total_amount" id="total_amount_hidden" value="0">
                                </div>
                                
                                <div class="mt-4">
                                    <x-input-label for="paid_amount" :value="__('Uang yang Dibayarkan (Rp)')" />
                                    <x-text-input id="paid_amount" class="block mt-1 w-full text-xl" type="number" step="1" name="paid_amount" :value="old('paid_amount')" required min="0" oninput="calculateChange()" />
                                    <x-input-error :messages="$errors->get('paid_amount')" class="mt-2" />
                                </div>

                                <div class="mt-4 pt-4 border-t">
                                    <label class="block font-medium text-sm text-gray-700">Kembalian:</label>
                                    <p id="change-display" class="text-xl font-bold text-green-600">Rp 0</p>
                                </div>
                                
                                <div class="flex items-center justify-end mt-6">
                                    <x-primary-button id="process-sale-button" disabled>
                                        {{ __('Proses Penjualan') }}
                                    </x-primary-button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    
    {{-- LOGIC JAVASCRIPT --}}
    <script>
        let cart = {}; // {productId: {name, price, quantity, stock}}
        let itemIndex = 0; 
        const MAX_STOCK_CHECK = 1000; 

        document.addEventListener('DOMContentLoaded', function() {
            // EVENT LISTENER DIPASANG KE ID 'product_select'
            document.getElementById('product_select').addEventListener('change', function() {
                const select = this;
                const productId = select.value;

                if (productId && !cart.hasOwnProperty(productId)) {
                    const selectedOption = select.options[select.selectedIndex];
                    const price = parseFloat(selectedOption.dataset.price);
                    const name = selectedOption.dataset.name;
                    const stock = parseInt(selectedOption.dataset.stock);

                    addItemToCart(productId, name, price, stock);
                    select.value = ''; 
                } else if (productId) {
                    alert('Produk sudah ada di keranjang. Silakan ubah kuantitasnya.');
                    select.value = '';
                }
            });
            
            document.getElementById('paid_amount').addEventListener('input', calculateChange);
        });

        // 1. Menambahkan item baru ke keranjang
        function addItemToCart(id, name, price, stock) {
            if (stock <= 0) {
                 alert('Stok produk ini habis!');
                 return;
            }
            
            cart[id] = {
                id: id,
                name: name,
                price: price,
                quantity: 1,
                stock: stock,
                index: itemIndex++
            };
            
            renderCart();
            calculateGrandTotal();
        }

        // 2. Mengubah kuantitas item
        function updateQuantity(id, element) {
            let qty = parseInt(element.value);
            
            if (qty <= 0 || isNaN(qty)) {
                qty = 1;
                element.value = 1;
            }

            if (qty > cart[id].stock) {
                alert(`Stok hanya tersisa ${cart[id].stock}!`);
                qty = cart[id].stock;
                element.value = qty;
            }

            cart[id].quantity = qty;
            renderCart();
            calculateGrandTotal();
        }

        // 3. Menghapus item dari keranjang
        function removeItem(id) {
            if (confirm('Yakin ingin menghapus item ini?')) {
                delete cart[id];
                renderCart();
                calculateGrandTotal();
            }
        }

        // 4. Merender ulang tabel keranjang
        function renderCart() {
            const cartBody = document.getElementById('cart-body');
            cartBody.innerHTML = ''; 
            let hasItems = false;

            for (const id in cart) {
                hasItems = true;
                const item = cart[id];
                const subtotal = item.price * item.quantity;
                
                const newRow = document.createElement('tr');
                newRow.className = 'item-row';
                newRow.innerHTML = `
                    <td class="px-6 py-4">
                        ${item.name}
                        <input type="hidden" name="items[${item.index}][product_id]" value="${item.id}">
                    </td>
                    <td class="px-6 py-4 text-center">Rp ${item.price.toLocaleString('id-ID')}</td>
                    <td class="px-6 py-4">
                        <input type="number" 
                                name="items[${item.index}][quantity]" 
                                value="${item.quantity}" 
                                min="1" 
                                max="${item.stock > MAX_STOCK_CHECK ? MAX_STOCK_CHECK : item.stock}"
                                class="w-20 text-center border-gray-300 rounded-md shadow-sm"
                                oninput="updateQuantity(${item.id}, this)">
                        <input type="hidden" name="items[${item.index}][price_per_unit]" value="${item.price}">
                        <input type="hidden" name="items[${item.index}][subtotal]" value="${subtotal.toFixed(2)}">
                    </td>
                    <td class="px-6 py-4 text-right subtotal-display">Rp ${subtotal.toLocaleString('id-ID')}</td>
                    <td class="px-6 py-4 text-center">
                        <button type="button" onclick="removeItem(${item.id})" class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
                    </td>
                `;
                cartBody.appendChild(newRow);
            }
            
            const emptyRow = document.getElementById('empty-cart-row');
            if (emptyRow) {
                emptyRow.style.display = hasItems ? 'none' : 'table-row';
            }
            
            document.getElementById('process-sale-button').disabled = !hasItems;
        }

        // 5. Menghitung Total Harga Keseluruhan
        function calculateGrandTotal() {
            let total = 0;
            for (const id in cart) {
                total += cart[id].price * cart[id].quantity;
            }

            document.getElementById('grand-total-display').innerText = `Rp ${total.toLocaleString('id-ID')}`;
            document.getElementById('total_amount_hidden').value = total.toFixed(2);
            
            calculateChange();
        }

        // 6. Menghitung Kembalian
        function calculateChange() {
            const totalAmount = parseFloat(document.getElementById('total_amount_hidden').value) || 0;
            const paidAmount = parseFloat(document.getElementById('paid_amount').value) || 0;
            let change = paidAmount - totalAmount;

            const processButton = document.getElementById('process-sale-button');
            const changeDisplay = document.getElementById('change-display');
            
            if (totalAmount <= 0) {
                 processButton.disabled = true;
                 changeDisplay.innerText = `Rp 0`;
                 changeDisplay.className = 'text-xl font-bold text-green-600'; 
                 return;
            }


            if (change < 0) {
                changeDisplay.innerText = `Rp ${change.toLocaleString('id-ID')}`;
                changeDisplay.className = 'text-xl font-bold text-red-600'; 
                processButton.disabled = true; // Uang kurang, tidak bisa proses
            } else {
                changeDisplay.innerText = `Rp ${change.toLocaleString('id-ID')}`;
                changeDisplay.className = 'text-xl font-bold text-green-600';
                processButton.disabled = !Object.keys(cart).length; // Uang cukup, bisa proses
            }
        }
        
        // Render pertama kali (pastikan tabel kosong)
        renderCart();
    </script>
</x-app-layout>