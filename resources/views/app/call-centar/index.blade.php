<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Call Centar
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Pretraga porudžbina, detalji, otkazivanje, resend email, audit log.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('app.home') }}"
                   class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Nazad
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Quick stats --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @php
                    $stats = $stats ?? [
                        ['label' => 'Danas', 'value' => $stats['today'] ?? '—', 'hint' => 'Nove porudžbine'],
                        ['label' => 'U obradi', 'value' => $stats['processing'] ?? '—', 'hint' => 'Aktivne'],
                        ['label' => 'Za štampu', 'value' => $stats['to_print'] ?? '—', 'hint' => 'Neštampane'],
                        ['label' => 'Problemi', 'value' => $stats['issues'] ?? '—', 'hint' => 'Potrebna pažnja'],
                    ];
                @endphp

                @foreach ($stats as $s)
                    <div class="bg-white shadow-sm rounded-lg p-4 border border-gray-100">
                        <div class="text-sm text-gray-500">{{ $s['label'] }}</div>
                        <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $s['value'] }}</div>
                        <div class="mt-1 text-xs text-gray-500">{{ $s['hint'] }}</div>
                    </div>
                @endforeach
            </div>

            {{-- Search + filters --}}
            <div class="bg-white shadow-sm rounded-lg border border-gray-100">
                <div class="p-6">
                    <form method="GET" action="{{ url()->current() }}" class="space-y-4">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
                            <div class="lg:col-span-5">
                                <label class="block text-sm font-medium text-gray-700">Pretraga</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input
                                        type="text"
                                        name="q"
                                        value="{{ request('q') }}"
                                        placeholder="Broj porudžbine, email, telefon, ime…"
                                        class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    Tip: probaj i: <span class="font-mono">#1234</span>, <span class="font-mono">john@</span>, <span class="font-mono">+381</span>
                                </p>
                            </div>

                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    @php $status = request('status'); @endphp
                                    <option value="">Svi</option>
                                    <option value="pending" @selected($status==='pending')>Pending</option>
                                    <option value="processing" @selected($status==='processing')>Processing</option>
                                    <option value="completed" @selected($status==='completed')>Completed</option>
                                    <option value="cancelled" @selected($status==='cancelled')>Cancelled</option>
                                    <option value="refunded" @selected($status==='refunded')>Refunded</option>
                                    <option value="failed" @selected($status==='failed')>Failed</option>
                                </select>
                            </div>

                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Kanal</label>
                                <select name="channel" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    @php $channel = request('channel'); @endphp
                                    <option value="">Svi</option>
                                    <option value="web" @selected($channel==='web')>Web</option>
                                    <option value="callcenter" @selected($channel==='callcenter')>Call centar</option>
                                    <option value="marketplace" @selected($channel==='marketplace')>Marketplace</option>
                                </select>
                            </div>

                            <div class="lg:col-span-3">
                                <label class="block text-sm font-medium text-gray-700">Period</label>
                                <div class="mt-1 grid grid-cols-2 gap-2">
                                    <input type="date" name="from" value="{{ request('from') }}" class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                                    <input type="date" name="to" value="{{ request('to') }}" class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <button
                                    type="submit"
                                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                                >
                                    Pretraži
                                </button>

                                <a
                                    href="{{ url()->current() }}"
                                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    Reset
                                </a>
                            </div>

                            <div class="text-xs text-gray-500">
                                @php $count = $total ?? ($orders->total() ?? ($orders->count() ?? 0)); @endphp
                                Prikazano: <span class="font-semibold text-gray-700">{{ $count }}</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Results + Details --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                {{-- Left: results table --}}
                <div class="lg:col-span-7 bg-white shadow-sm rounded-lg border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <div class="text-sm font-semibold text-gray-900">Rezultati</div>
                            <div class="text-xs text-gray-500">Klik na red → detalji desno.</div>
                        </div>

                        <div class="flex items-center gap-2">
                            <select name="perPage" onchange="location = this.value" class="rounded-md border-gray-300 text-sm">
                                @php $per = (int)request('perPage', 25); @endphp
                                @foreach ([10,25,50,100] as $p)
                                    <option value="{{ request()->fullUrlWithQuery(['perPage'=>$p]) }}" @selected($per===$p)>{{ $p }}/str</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Porudžbina</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kupac</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Iznos</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Datum</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-100">
                                @php
                                    $orders = $orders ?? collect();
                                    $selectedId = request('order_id');
                                @endphp

                                @forelse ($orders as $o)
                                    @php
                                        $id = $o->id ?? $o['id'] ?? null;
                                        $number = $o->number ?? $o['number'] ?? $id;
                                        $customer = $o->customer_name ?? $o['customer_name'] ?? ($o->billing_name ?? $o['billing_name'] ?? '—');
                                        $email = $o->customer_email ?? $o['customer_email'] ?? ($o->billing_email ?? $o['billing_email'] ?? null);
                                        $statusVal = $o->status ?? $o['status'] ?? '—';
                                        $totalVal = $o->total ?? $o['total'] ?? '—';
                                        $created = $o->created_at ?? $o['created_at'] ?? null;

                                        $isActive = (string)$selectedId === (string)$id;
                                        $rowUrl = request()->fullUrlWithQuery(['order_id'=>$id]);
                                    @endphp

                                    <tr class="{{ $isActive ? 'bg-indigo-50' : 'hover:bg-gray-50' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ $rowUrl }}" class="font-semibold text-gray-900 hover:text-indigo-700">
                                                #{{ $number }}
                                            </a>
                                            <div class="text-xs text-gray-500">
                                                ID: {{ $id ?? '—' }}
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $customer }}</div>
                                            <div class="text-xs text-gray-500">{{ $email ?? '—' }}</div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $badge = match((string)$statusVal) {
                                                    'completed' => 'bg-green-100 text-green-800',
                                                    'processing' => 'bg-blue-100 text-blue-800',
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'cancelled' => 'bg-gray-100 text-gray-800',
                                                    'refunded' => 'bg-purple-100 text-purple-800',
                                                    'failed' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $badge }}">
                                                {{ strtoupper((string)$statusVal) }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ is_numeric($totalVal) ? number_format((float)$totalVal, 2, ',', '.') : $totalVal }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                            {{ $created ? \Illuminate\Support\Carbon::parse($created)->format('d.m.Y H:i') : '—' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-14 text-center">
                                            <div class="text-sm font-semibold text-gray-900">Nema rezultata</div>
                                            <div class="mt-1 text-sm text-gray-500">Promeni filtere ili unesi drugačiji upit.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-100">
                        @if (method_exists($orders, 'links'))
                            {{ $orders->withQueryString()->links() }}
                        @else
                            <div class="text-xs text-gray-500">Pagination će se pojaviti kad `$orders` bude paginator.</div>
                        @endif
                    </div>
                </div>

                {{-- Right: details panel --}}
                <div class="lg:col-span-5 space-y-6">
                    <div class="bg-white shadow-sm rounded-lg border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">Detalji porudžbine</div>
                                    <div class="text-xs text-gray-500">Brze akcije + pregled stavki.</div>
                                </div>

                                @if (!empty($order))
                                    <a href="{{ route('app.print', ['order_id' => $order->id ?? $order['id'] ?? null]) }}"
                                       class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        Štampa
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="p-6">
                            @if (empty($order))
                                <div class="rounded-lg border border-dashed border-gray-300 p-6 text-center">
                                    <div class="text-sm font-semibold text-gray-900">Izaberi porudžbinu</div>
                                    <div class="mt-1 text-sm text-gray-500">Klikni na red iz tabele levo da vidiš detalje.</div>
                                </div>
                            @else
                                @php
                                    $oid = $order->id ?? $order['id'] ?? null;
                                    $onum = $order->number ?? $order['number'] ?? $oid;
                                    $cust = $order->customer_name ?? $order['customer_name'] ?? ($order->billing_name ?? $order['billing_name'] ?? '—');
                                    $mail = $order->customer_email ?? $order['customer_email'] ?? ($order->billing_email ?? $order['billing_email'] ?? '—');
                                    $phone = $order->customer_phone ?? $order['customer_phone'] ?? ($order->billing_phone ?? $order['billing_phone'] ?? '—');
                                    $statusVal = $order->status ?? $order['status'] ?? '—';
                                    $totalVal = $order->total ?? $order['total'] ?? '—';
                                    $items = $order->items ?? $order['items'] ?? [];
                                @endphp

                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="text-lg font-semibold text-gray-900">#{{ $onum }}</div>
                                        <div class="mt-1 text-sm text-gray-600">{{ $cust }}</div>
                                        <div class="text-sm text-gray-500">{{ $mail }}</div>
                                        <div class="text-sm text-gray-500">{{ $phone }}</div>
                                    </div>

                                    <div class="text-right">
                                        <div class="text-sm text-gray-500">Iznos</div>
                                        <div class="text-lg font-semibold text-gray-900">
                                            {{ is_numeric($totalVal) ? number_format((float)$totalVal, 2, ',', '.') : $totalVal }}
                                        </div>
                                        <div class="mt-2">
                                            @php
                                                $badge = match((string)$statusVal) {
                                                    'completed' => 'bg-green-100 text-green-800',
                                                    'processing' => 'bg-blue-100 text-blue-800',
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'cancelled' => 'bg-gray-100 text-gray-800',
                                                    'refunded' => 'bg-purple-100 text-purple-800',
                                                    'failed' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $badge }}">
                                                {{ strtoupper((string)$statusVal) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <form method="POST" action="{{ route('app.send') }}">
                                        @csrf
                                        <input type="hidden" name="order_id" value="{{ $oid }}">
                                        <button type="submit"
                                            class="w-full inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                                            Resend email
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('app.pickup') }}">
                                        @csrf
                                        <input type="hidden" name="order_id" value="{{ $oid }}">
                                        <button type="submit"
                                            class="w-full inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            Obeleži kao preuzeto
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('app.call-centar') }}">
                                        @csrf
                                        <input type="hidden" name="order_id" value="{{ $oid }}">
                                        <input type="hidden" name="action" value="cancel">
                                        <button type="submit"
                                            class="w-full inline-flex items-center justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500"
                                            onclick="return confirm('Otkazati porudžbinu #{{ $onum }}?')">
                                            Otkaži
                                        </button>
                                    </form>

                                    <a href="{{ route('app.print', ['order_id' => $oid]) }}"
                                       class="w-full inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        Otvori štampu
                                    </a>
                                </div>

                                {{-- Items --}}
                                <div class="mt-6">
                                    <div class="text-sm font-semibold text-gray-900">Stavke</div>
                                    <div class="mt-3 space-y-3">
                                        @forelse ($items as $it)
                                            @php
                                                $name = $it->name ?? $it['name'] ?? 'Artikal';
                                                $qty = $it->quantity ?? $it['quantity'] ?? 1;
                                                $sku = $it->sku ?? $it['sku'] ?? null;
                                                $price = $it->price ?? $it['price'] ?? null;
                                            @endphp
                                            <div class="flex items-start justify-between gap-3 rounded-lg border border-gray-100 p-3">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $name }}</div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $sku ? 'SKU: '.$sku.' • ' : '' }}Qty: {{ $qty }}
                                                    </div>
                                                </div>
                                                <div class="text-sm font-semibold text-gray-900">
                                                    {{ $price !== null && is_numeric($price) ? number_format((float)$price, 2, ',', '.') : ($price ?? '—') }}
                                                </div>
                                            </div>
                                        @empty
                                            <div class="rounded-lg border border-dashed border-gray-300 p-4 text-sm text-gray-500">
                                                Nema stavki (dodaće se kad model bude punjen).
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Audit log / timeline --}}
                    <div class="bg-white shadow-sm rounded-lg border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <div class="text-sm font-semibold text-gray-900">Audit log</div>
                            <div class="text-xs text-gray-500">Pregled promena i webhook događaja.</div>
                        </div>

                        <div class="p-6">
                            @php
                                $logs = $logs ?? ($order->logs ?? null) ?? [];
                            @endphp

                            @if (empty($order))
                                <div class="text-sm text-gray-500">Izaberi porudžbinu da vidiš audit log.</div>
                            @else
                                <ol class="relative border-s border-gray-200 ps-4 space-y-4">
                                    @forelse ($logs as $log)
                                        @php
                                            $when = $log->created_at ?? $log['created_at'] ?? null;
                                            $title = $log->action ?? $log['action'] ?? 'Event';
                                            $meta = $log->meta ?? $log['meta'] ?? null;
                                            $by = $log->user_name ?? $log['user_name'] ?? ($log->source ?? $log['source'] ?? null);
                                        @endphp
                                        <li class="ms-2">
                                            <span class="absolute -start-1.5 mt-1.5 h-3 w-3 rounded-full bg-indigo-600"></span>
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900">{{ $title }}</div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $by ? $by.' • ' : '' }}
                                                        {{ $when ? \Illuminate\Support\Carbon::parse($when)->format('d.m.Y H:i') : '—' }}
                                                    </div>
                                                    @if ($meta)
                                                        <div class="mt-2 text-xs font-mono text-gray-600 bg-gray-50 rounded p-2 overflow-auto">
                                                            {{ is_string($meta) ? $meta : json_encode($meta, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                    @empty
                                        <div class="text-sm text-gray-500">Nema zapisa.</div>
                                    @endforelse
                                </ol>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>