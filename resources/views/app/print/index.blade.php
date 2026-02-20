<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Štampa
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Neštampane, bulk selekcija, istorija štampe.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('app.home') }}"
                   class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Home
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Tabs --}}
            @php
                $tab = request('tab', 'unprinted');
                $tabs = [
                    'unprinted' => 'Neštampane',
                    'bulk' => 'Bulk štampa',
                    'history' => 'Istorija',
                ];
            @endphp

            <div class="bg-white shadow-sm rounded-lg border border-gray-100">
                <div class="px-6 pt-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-2">
                            @foreach ($tabs as $key => $label)
                                <a href="{{ request()->fullUrlWithQuery(['tab' => $key]) }}"
                                   class="inline-flex items-center px-3 py-2 text-sm font-semibold rounded-md
                                   {{ $tab === $key ? 'bg-indigo-600 text-white' : 'bg-gray-50 text-gray-700 hover:bg-gray-100' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>

                        <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                            <input type="hidden" name="tab" value="{{ $tab }}">
                            <input
                                name="q"
                                value="{{ request('q') }}"
                                placeholder="Pretraga (#, email, ime)…"
                                class="w-full sm:w-64 rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <button class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                                Traži
                            </button>
                        </form>
                    </div>
                </div>

                <div class="p-6">
                    @if ($tab === 'unprinted')
                        @php $unprinted = $unprinted ?? collect(); @endphp

                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="text-sm font-semibold text-gray-900">Neštampane porudžbine</div>
                                <div class="text-xs text-gray-500">Označi više i štampaj odjednom.</div>
                            </div>

                            <form method="POST" action="{{ url()->current() }}" class="flex items-center gap-2">
                                @csrf
                                <input type="hidden" name="action" value="print_selected">
                                <button type="submit"
                                    class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">
                                    Štampaj označene
                                </button>
                            </form>
                        </div>

                        <div class="mt-4 overflow-x-auto border border-gray-100 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-100">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3">
                                            <input type="checkbox" id="checkAll" class="rounded border-gray-300">
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Porudžbina</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kupac</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Iznos</th>
                                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Datum</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @forelse ($unprinted as $o)
                                        @php
                                            $id = $o->id ?? $o['id'] ?? null;
                                            $num = $o->number ?? $o['number'] ?? $id;
                                            $cust = $o->customer_name ?? $o['customer_name'] ?? '—';
                                            $total = $o->total ?? $o['total'] ?? '—';
                                            $created = $o->created_at ?? $o['created_at'] ?? null;
                                        @endphp
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-4">
                                                <input type="checkbox" name="order_ids[]" value="{{ $id }}" class="rowCheck rounded border-gray-300">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a class="font-semibold text-gray-900 hover:text-indigo-700"
                                                   href="{{ route('app.call-centar', ['order_id' => $id]) }}">
                                                    #{{ $num }}
                                                </a>
                                                <div class="text-xs text-gray-500">Neštampano</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $cust }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ is_numeric($total) ? number_format((float)$total, 2, ',', '.') : $total }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                                {{ $created ? \Illuminate\Support\Carbon::parse($created)->format('d.m.Y H:i') : '—' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-14 text-center">
                                                <div class="text-sm font-semibold text-gray-900">Nema neštampanih</div>
                                                <div class="mt-1 text-sm text-gray-500">Kad dođu nove porudžbine, pojaviće se ovde.</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', () => {
                                const checkAll = document.getElementById('checkAll');
                                const rowChecks = Array.from(document.querySelectorAll('.rowCheck'));
                                if (checkAll) {
                                    checkAll.addEventListener('change', () => {
                                        rowChecks.forEach(ch => ch.checked = checkAll.checked);
                                    });
                                }
                            });
                        </script>

                    @elseif ($tab === 'bulk')
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                            <div class="lg:col-span-5 rounded-lg border border-gray-100 p-5 bg-gray-50">
                                <div class="text-sm font-semibold text-gray-900">Bulk štampa</div>
                                <div class="mt-1 text-sm text-gray-600">Nalepi više brojeva porudžbina (po liniji) i štampaj.</div>

                                <form method="POST" action="{{ url()->current() }}" class="mt-4 space-y-3">
                                    @csrf
                                    <input type="hidden" name="action" value="bulk_print">
                                    <textarea
                                        name="bulk"
                                        rows="10"
                                        placeholder="#12345&#10;#12346&#10;#12347"
                                        class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                    >{{ old('bulk') }}</textarea>
                                    <x-input-error :messages="$errors->get('bulk')" class="mt-2" />

                                    <button type="submit"
                                        class="inline-flex items-center justify-center rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">
                                        Štampaj
                                    </button>
                                </form>

                                <div class="mt-4 text-xs text-gray-500">
                                    Tip: dozvoli i unos bez “#” (UI ne smeta, parser u backendu rešava).
                                </div>
                            </div>

                            <div class="lg:col-span-7">
                                <div class="bg-white shadow-sm rounded-lg border border-gray-100 overflow-hidden">
                                    <div class="px-6 py-4 border-b border-gray-100">
                                        <div class="text-sm font-semibold text-gray-900">Preview rezultata</div>
                                        <div class="text-xs text-gray-500">Ovde ćeš prikazivati koje porudžbine su validne / nisu nađene.</div>
                                    </div>

                                    <div class="p-6">
                                        <div class="rounded-lg border border-dashed border-gray-300 p-6 text-center">
                                            <div class="text-sm font-semibold text-gray-900">Spremno za backend</div>
                                            <div class="mt-1 text-sm text-gray-500">
                                                Kad obradiš `bulk` listu, prikaži: pronađene / promašene / već štampane.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @elseif ($tab === 'history')
                        @php $printed = $printed ?? collect(); @endphp

                        <div class="text-sm font-semibold text-gray-900">Istorija štampe</div>
                        <div class="mt-1 text-sm text-gray-600">Ko je i kada štampao, i šta.</div>

                        <div class="mt-4 overflow-x-auto border border-gray-100 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-100">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Porudžbina</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Korisnik</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tip</th>
                                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Vreme</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @forelse ($printed as $p)
                                        @php
                                            $num = $p->order_number ?? $p['order_number'] ?? '—';
                                            $by = $p->user_name ?? $p['user_name'] ?? '—';
                                            $type = $p->type ?? $p['type'] ?? 'label';
                                            $when = $p->created_at ?? $p['created_at'] ?? null;
                                        @endphp
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="font-semibold text-gray-900">#{{ $num }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $by }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ strtoupper($type) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                                {{ $when ? \Illuminate\Support\Carbon::parse($when)->format('d.m.Y H:i') : '—' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-14 text-center">
                                                <div class="text-sm font-semibold text-gray-900">Nema istorije</div>
                                                <div class="mt-1 text-sm text-gray-500">Kad krene štampa, ovde će se beležiti.</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>