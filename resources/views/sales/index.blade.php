<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Transaksi Penjualan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('sales.index') }}" method="GET" class="mb-6 p-4 border rounded-lg bg-gray-50">
    <div class="flex space-x-4 items-end">
        
        <div class="flex-1">
            <label for="search" class="block text-sm font-medium text-gray-700">Cari Invoice/Kasir</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Kode Invoice atau Nama Kasir" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
        
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
            <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
        
        <div>
            <label for="end_date" class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
            <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>
        
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
            Filter
        </button>
        <a href="{{ route('sales.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded">
            Reset
        </a>
        
        <a href="{{ route('sales.export') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
    Export PDF
</a>
        
    </div>
</form>
                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kasir</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibayar</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($sales as $sale)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ $sale->invoice_code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $sale->user->name ?? 'System' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Rp. {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Rp. {{ number_format($sale->paid_amount, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $sale->created_at->format('d M Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $sales->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>