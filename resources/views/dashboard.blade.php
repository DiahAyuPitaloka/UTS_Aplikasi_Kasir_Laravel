<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                
                <div class="bg-white p-5 rounded-lg shadow">
                    <p class="text-sm text-gray-500">Transaksi Hari Ini</p>
                    <h3 class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format($total_transactions_today ?? 0) }}</h3>
                </div>

                <div class="bg-white p-5 rounded-lg shadow">
                    <p class="text-sm text-gray-500">Pendapatan Hari Ini</p>
                    <h3 class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($total_revenue_today ?? 0, 0, ',', '.') }}</h3>
                </div>

                <div class="bg-white p-5 rounded-lg shadow">
                    <p class="text-sm text-gray-500">Total User Kasir</p>
                    <h3 class="text-2xl font-bold text-gray-600 mt-1">{{ number_format($total_kasir ?? 0) }}</h3>
                </div>

                <div class="bg-white p-5 rounded-lg shadow">
                    <p class="text-sm text-gray-500">Produk Stok Rendah (< 5)</p>
                    <h3 class="text-2xl font-bold text-red-600 mt-1">{{ number_format($low_stock_products->count() ?? 0) }}</h3>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-3">5 Transaksi Terbaru</h3>
                    @if ($latest_sales && $latest_sales->count())
                        @else
                        <p class="text-gray-500">Belum ada data transaksi yang tercatat.</p>
                    @endif
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>