<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Porudžbina #{{ $order->order_number ?? $order->woo_order_id }}
                </h2>
                <div class="text-sm text-gray-600">
                    woo: {{ $order->woo_order_id }} • {{ $order->created_at?->format('d.m.Y H:i') }}
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('app.call-centar', request()->only(['q','status','printed','from','to'])) }}"
                   class="inline-flex items-center px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-xs text-gray-700 hover:bg-gray-200">
                    ← Nazad
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Summary --}}
            <div class="bg-white shadow-sm rounded-lg p-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <div class="text-xs uppercase text-gray-500">Kupac</div>
                        <div class="text-lg font-semibold text-gray-900">{{ $order->customer_name }}</div>
                        <div class="text-sm text-gray-700">{{ $order->phone ?? '—' }}</div>
                        <div class="text-sm text-gray-700">{{ $order->email ?? '—' }}</div>
                    </div>

                    <div>
                        <div class="text-xs uppercase text-gray-500">Adresa</div>
                        <div class="text-sm text-gray-900">{{ $order->address ?? '—' }}</div>
                        <div class="text-sm text-gray-700">{{ $order->postcode ?? '' }} {{ $order->city ?? '' }}</div>

                        @if($order->termal_code)
                            <div class="mt-2 text-sm">
                                <span class="text-gray-500">Termal:</span>
                                <span class="font-medium text-gray-900">{{ $order->termal_code }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="md:text-right">
                        <div class="text-xs uppercase text-gray-500">Status</div>
                        <div class="inline-flex items-center px-3 py-1 rounded bg-gray-100 text-gray-900 text-sm">
                            {{ $order->status }}
                        </div>

                        <div class="mt-3 text-xs uppercase text-gray-500">Iznos</div>
                        <div class="text-lg font-semibold text-gray-900">
                            {{ number_format((float)$order->total, 2, ',', '.') }} {{ $order->currency }}
                        </div>

                        <div class="text-sm text-gray-600">
                            PDV: {{ number_format((float)$order->tax_total, 2, ',', '.') }} {{ $order->currency }}
                        </div>

                        <div class="mt-2">
                            @if($order->is_printed)
                                <span class="inline-flex items-center px-2 py-1 rounded bg-green-100 text-green-800 text-xs">
                                    Štampano
                                </span>
                                @if($order->printed_at)
                                    <span class="text-xs text-gray-500">({{ $order->printed_at->format('d.m.Y H:i') }})</span>
                                @endif
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs">
                                    Neštampano
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($order->office_notice)
                    <div class="mt-5 border-t pt-4">
                        <div class="text-xs uppercase text-gray-500">Office notice</div>
                        <div class="mt-1 text-gray-900">{{ $order->office_notice }}</div>
                        @if($order->office_notice_at)
                            <div class="text-xs text-gray-500 mt-1">{{ $order->office_notice_at->format('d.m.Y H:i') }}</div>
                        @endif
                    </div>
                @endif

                @if(is_array($order->tracking_numbers) && count($order->tracking_numbers))
                    <div class="mt-5 border-t pt-4">
                        <div class="text-xs uppercase text-gray-500">Tracking</div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($order->tracking_numbers as $tn)
                                <span class="inline-flex items-center px-2 py-1 rounded bg-blue-50 text-blue-800 text-xs">
                                    {{ $tn }}
                                </span>
                            @endforeach
                        </div>
                        @if($order->tracking_updated_at)
                            <div class="text-xs text-gray-500 mt-1">{{ $order->tracking_updated_at->format('d.m.Y H:i') }}</div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Items --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-5 border-b">
                    <div class="text-lg font-semibold text-gray-900">Stavke</div>
                    <div class="text-sm text-gray-600">Ukupno: {{ $order->items->count() }}</div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="text-left px-4 py-3">Naziv</th>
                                <th class="text-left px-4 py-3">SKU</th>
                                <th class="text-left px-4 py-3">EAN</th>
                                <th class="text-right px-4 py-3">Količina</th>
                                <th class="text-right px-4 py-3">Cena</th>
                                <th class="text-right px-4 py-3">PDV</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            @forelse($order->items as $it)
                                <tr>
                                    <td class="px-4 py-3 text-gray-900">{{ $it->name }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $it->sku ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $it->ean ?? '—' }}</td>
                                    <td class="px-4 py-3 text-right text-gray-900">{{ $it->qty }}</td>
                                    <td class="px-4 py-3 text-right text-gray-900">
                                        {{ number_format((float)$it->line_total, 2, ',', '.') }} {{ $order->currency }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-700">
                                        {{ number_format((float)$it->line_tax, 2, ',', '.') }} {{ $order->currency }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-gray-600">
                                        Nema stavki za ovu porudžbinu.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>