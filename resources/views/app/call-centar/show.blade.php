<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Porudžbina #{{ $order->order_number ?? $order->woo_order_id }}
            </h2>

            <div class="text-sm text-gray-600">
                Status:
                <span class="font-semibold">{{ $order->status }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if ($errors->has('status'))
                <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3">
                    {{ $errors->first('status') }}
                </div>
            @endif

            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3">
                    {{ session('status') }}
                </div>
            @endif

            {{-- OFFICE NOTICE (iz Woo admina) --}}
            @if (!empty($order->office_notice))
                <div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-lg px-4 py-3">
                    <div class="font-semibold">Napomena kancelarije</div>
                    <div class="text-sm whitespace-pre-line">{{ $order->office_notice }}</div>
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm text-gray-500">Kupac</div>
                        <div class="text-lg font-semibold text-gray-900">
                            {{ $order->customer_name }}
                        </div>
                        <div class="text-sm text-gray-700">
                            Telefon: <span class="font-medium">{{ $order->phone ?: '—' }}</span>
                        </div>
                        <div class="text-sm text-gray-700">
                            Email: <span class="font-medium">{{ $order->email ?: '—' }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Adresa</div>
                        <div class="text-sm text-gray-800">
                            {{ $order->address ?: '—' }}<br>
                            {{ $order->postcode ?: '' }} {{ $order->city ?: '' }}
                        </div>

                        <div class="mt-3 text-sm text-gray-700">
                            Termal kod: <span class="font-semibold">{{ $order->termal_code ?: '—' }}</span>
                        </div>
                    </div>
                </div>

                @php
                    $canComplete = $order->status !== 'cancelled' && $order->status !== 'completed';
                    $canCancel = $order->status !== 'completed' && $order->status !== 'cancelled';
                @endphp

                <div class="mt-6 flex flex-wrap gap-2">
                    <button
                        type="button"
                        class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                        id="openPickupModalBtn"
                    >
                        Pickup modal
                    </button>

                    <form method="POST" action="{{ route('app.call-centar.complete', $order) }}">
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-md px-4 py-2 text-sm font-semibold text-white
                                {{ $canComplete ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-emerald-300 cursor-not-allowed' }}"
                            {{ $canComplete ? '' : 'disabled' }}
                        >
                            Mark pickup complete
                        </button>
                    </form>

                    <form method="POST" action="{{ route('app.call-centar.cancel', $order) }}"
                          onsubmit="return confirm('Sigurno otkazati porudžbinu?');">
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-md px-4 py-2 text-sm font-semibold text-white
                                {{ $canCancel ? 'bg-red-600 hover:bg-red-700' : 'bg-red-300 cursor-not-allowed' }}"
                            {{ $canCancel ? '' : 'disabled' }}
                        >
                            Otkaži porudžbinu
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Stavke</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-gray-600 border-b">
                            <tr>
                                <th class="py-2 pr-4">Naziv</th>
                                <th class="py-2 pr-4">SKU</th>
                                <th class="py-2 pr-4">EAN</th>
                                <th class="py-2 pr-4 text-right">Količina</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($order->items as $it)
                                <tr>
                                    <td class="py-2 pr-4 text-gray-900">{{ $it->name }}</td>
                                    <td class="py-2 pr-4 text-gray-700">{{ $it->sku ?: '—' }}</td>
                                    <td class="py-2 pr-4 text-gray-700">{{ $it->ean ?: '—' }}</td>
                                    <td class="py-2 pr-4 text-right font-semibold">{{ $it->qty }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-3 text-gray-600">Nema stavki.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL --}}
    <x-simple-modal id="pickupModal" title="Pickup – potvrda" :open="request()->boolean('pickup')">
        <div class="space-y-4">

            @if (!empty($order->office_notice))
                <div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-lg p-3">
                    <div class="font-semibold">Napomena kancelarije</div>
                    <div class="text-sm whitespace-pre-line">{{ $order->office_notice }}</div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-xs text-gray-500">Ime i prezime</div>
                    <div class="font-semibold text-gray-900">{{ $order->customer_name }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-xs text-gray-500">Telefon</div>
                    <div class="font-semibold text-gray-900">{{ $order->phone ?: '—' }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-xs text-gray-500">Email</div>
                    <div class="font-semibold text-gray-900">{{ $order->email ?: '—' }}</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="text-xs text-gray-500">Termal kod</div>
                    <div class="font-semibold text-gray-900">{{ $order->termal_code ?: '—' }}</div>
                </div>
            </div>

            <div class="border rounded-lg overflow-hidden">
                <div class="px-3 py-2 bg-gray-50 border-b text-sm font-semibold text-gray-800">
                    Stavke
                </div>
                <div class="p-3">
                    <ul class="space-y-2">
                        @foreach ($order->items as $it)
                            <li class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $it->name }}</div>
                                    <div class="text-xs text-gray-500">
                                        SKU: {{ $it->sku ?: '—' }} · EAN: {{ $it->ean ?: '—' }}
                                    </div>
                                </div>
                                <div class="font-semibold text-gray-900">x{{ $it->qty }}</div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            @php
                $canComplete = $order->status !== 'cancelled' && $order->status !== 'completed';
            @endphp

            <div class="flex flex-col sm:flex-row gap-2 pt-2">
                <form method="POST" action="{{ route('app.call-centar.complete', $order) }}" class="flex-1">
                    @csrf
                    <button
                        type="submit"
                        class="w-full inline-flex justify-center items-center rounded-md px-4 py-2 text-sm font-semibold text-white
                            {{ $canComplete ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-emerald-300 cursor-not-allowed' }}"
                        id="pickupCompleteBtn"
                        {{ $canComplete ? '' : 'disabled' }}
                    >
                        Mark as Pickup Complete
                    </button>
                </form>

                <button
                    type="button"
                    class="flex-1 inline-flex justify-center items-center rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-300"
                    data-modal-close
                >
                    Zatvori
                </button>
            </div>
        </div>
    </x-simple-modal>

    <script>
        (function () {
            const modal = document.getElementById('pickupModal');
            const openBtn = document.getElementById('openPickupModalBtn');

            function openModal() {
                if (!modal) return;
                modal.classList.remove('hidden');
                const btn = document.getElementById('pickupCompleteBtn');
                if (btn) btn.focus();
            }

            function closeModal() {
                if (!modal) return;
                modal.classList.add('hidden');
            }

            if (openBtn) openBtn.addEventListener('click', openModal);

            if (modal) {
                modal.querySelectorAll('[data-modal-close]').forEach(el => {
                    el.addEventListener('click', closeModal);
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
                });

                if (!modal.classList.contains('hidden')) {
                    const btn = document.getElementById('pickupCompleteBtn');
                    if (btn) btn.focus();
                }
            }
        })();
    </script>
</x-app-layout>