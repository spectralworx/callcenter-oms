<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Call Centar
            </h2>

            <a href="{{ route('app.home') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Nazad na Home
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Filters --}}
            <div class="bg-white shadow-sm rounded-lg p-4">
                <form method="GET" action="{{ route('app.call-centar') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    <div class="md:col-span-4">
                        <label class="block text-sm font-medium text-gray-700">Pretraga</label>
                        <input
                            type="text"
                            name="q"
                            value="{{ $filters['q'] }}"
                            placeholder="Ime, telefon, email, broj, termal…"
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Svi</option>
                            @foreach ($statuses as $s)
                                <option value="{{ $s }}" @selected($filters['status'] === $s)>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Štampa</label>
                        <select name="printed" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="" @selected($filters['printed'] === '')>Sve</option>
                            <option value="0" @selected($filters['printed'] === '0')>Neštampane</option>
                            <option value="1" @selected($filters['printed'] === '1')>Štampane</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Od</label>
                        <input
                            type="date"
                            name="from"
                            value="{{ $filters['from'] }}"
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Do</label>
                        <input
                            type="date"
                            name="to"
                            value="{{ $filters['to'] }}"
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>

                    <div class="md:col-span-12 flex gap-2 pt-2">
                        <button class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            Filtriraj
                        </button>

                        <a href="{{ route('app.call-centar') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="text-left px-4 py-3">Datum</th>
                                <th class="text-left px-4 py-3">Kupac</th>
                                <th class="text-left px-4 py-3">Kontakt</th>
                                <th class="text-left px-4 py-3">Broj</th>
                                <th class="text-left px-4 py-3">Status</th>
                                <th class="text-right px-4 py-3">Iznos</th>
                                <th class="text-left px-4 py-3">Štampa</th>
                                <th class="text-right px-4 py-3">Akcija</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            @forelse ($orders as $o)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                        {{ $o->created_at?->format('d.m.Y H:i') }}
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $o->customer_name }}</div>
                                        <div class="text-gray-600 text-xs">{{ $o->city }} {{ $o->postcode }}</div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="text-gray-900">{{ $o->phone ?? '—' }}</div>
                                        <div class="text-gray-600 text-xs">{{ $o->email ?? '—' }}</div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="text-gray-900">#{{ $o->order_number ?? $o->woo_order_id }}</div>
                                        <div class="text-gray-600 text-xs">woo: {{ $o->woo_order_id }}</div>
                                        @if($o->termal_code)
                                            <div class="text-gray-600 text-xs">termal: {{ $o->termal_code }}</div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 text-gray-800 text-xs">
                                            {{ $o->status }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-right font-medium text-gray-900 whitespace-nowrap">
                                        {{ number_format((float)$o->total, 2, ',', '.') }} {{ $o->currency }}
                                    </td>

                                    <td class="px-4 py-3">
                                        @if($o->is_printed)
                                            <span class="inline-flex items-center px-2 py-1 rounded bg-green-100 text-green-800 text-xs">
                                                Štampano
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs">
                                                Neštampano
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('app.call-centar.show', $o) }}"
                                           class="inline-flex items-center px-3 py-2 bg-gray-900 text-white rounded-md text-xs hover:bg-black">
                                            Detalji →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-10 text-center text-gray-600">
                                        Nema rezultata za izabrane filtere.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">
                    {{ $orders->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>