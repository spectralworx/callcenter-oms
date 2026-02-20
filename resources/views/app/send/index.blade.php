<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Pošalji porudžbinu
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                JOICO forma → resend email / slanje linka / potvrde.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">
                    <div class="font-semibold text-sm">Poslato</div>
                    <div class="text-sm">{{ session('status') }}</div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                {{-- Send form --}}
                <div class="lg:col-span-5 bg-white shadow-sm rounded-lg border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <div class="text-sm font-semibold text-gray-900">Resend</div>
                        <div class="text-xs text-gray-500">Unesi broj porudžbine ili email kupca.</div>
                    </div>

                    <div class="p-6 space-y-4">
                        <form method="POST" action="{{ url()->current() }}" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Porudžbina / Email</label>
                                <input
                                    name="q"
                                    value="{{ old('q', request('order_id') ? ('#'.request('order_id')) : '') }}"
                                    placeholder="#12345 ili email@…"
                                    class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                <x-input-error :messages="$errors->get('q')" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tip poruke</label>
                                    <select name="template" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                        @php $tpl = old('template', 'order_confirmation'); @endphp
                                        <option value="order_confirmation" @selected($tpl==='order_confirmation')>Potvrda porudžbine</option>
                                        <option value="payment_link" @selected($tpl==='payment_link')>Link za plaćanje</option>
                                        <option value="shipping_update" @selected($tpl==='shipping_update')>Update isporuke</option>
                                        <option value="custom" @selected($tpl==='custom')>Custom</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Primaoc</label>
                                    <select name="recipient" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                        @php $rcp = old('recipient', 'customer'); @endphp
                                        <option value="customer" @selected($rcp==='customer')>Kupac</option>
                                        <option value="billing" @selected($rcp==='billing')>Billing email</option>
                                        <option value="override" @selected($rcp==='override')>Ručni email</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Ručni email (opciono)</label>
                                <input
                                    type="email"
                                    name="override_email"
                                    value="{{ old('override_email') }}"
                                    placeholder="npr. kupac+test@domain.com"
                                    class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                <p class="mt-1 text-xs text-gray-500">Koristi samo ako je “Primaoc” = Ručni email.</p>
                                <x-input-error :messages="$errors->get('override_email')" class="mt-2" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Napomena (interno)</label>
                                <textarea
                                    name="note"
                                    rows="3"
                                    placeholder="Zašto šaljemo? (biće u audit log-u)"
                                    class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                >{{ old('note') }}</textarea>
                                <x-input-error :messages="$errors->get('note')" class="mt-2" />
                            </div>

                            <div class="flex flex-col sm:flex-row gap-3">
                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                                    Pošalji
                                </button>

                                <a href="{{ route('app.call-centar', ['order_id' => request('order_id')]) }}"
                                   class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    Otvori porudžbinu
                                </a>
                            </div>
                        </form>

                        <div class="rounded-lg bg-gray-50 border border-gray-100 p-4 text-sm text-gray-600">
                            <div class="font-semibold text-gray-900 text-sm">Kako radi</div>
                            <ul class="mt-2 list-disc ps-5 space-y-1 text-sm">
                                <li>Unos prepoznaje <span class="font-mono">#ID</span> ili email.</li>
                                <li>Sve akcije zabeleži u <span class="font-semibold">AuditLog</span>.</li>
                                <li>Kasnije dodaš preview email-a, ali UI je već spreman.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- History --}}
                <div class="lg:col-span-7 bg-white shadow-sm rounded-lg border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <div class="text-sm font-semibold text-gray-900">Istorija slanja</div>
                        <div class="text-xs text-gray-500">Poslednje resend akcije.</div>
                    </div>

                    @php $history = $history ?? collect(); @endphp

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Porudžbina</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Template</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Primaoc</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Korisnik</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Vreme</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse ($history as $h)
                                    @php
                                        $number = $h->order_number ?? $h['order_number'] ?? ($h->order_id ?? $h['order_id'] ?? '—');
                                        $tpl = $h->template ?? $h['template'] ?? '—';
                                        $to = $h->to ?? $h['to'] ?? '—';
                                        $by = $h->user_name ?? $h['user_name'] ?? '—';
                                        $when = $h->created_at ?? $h['created_at'] ?? null;
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a class="font-semibold text-gray-900 hover:text-indigo-700"
                                               href="{{ route('app.call-centar', ['order_id' => $h->order_id ?? $h['order_id'] ?? null]) }}">
                                                #{{ $number }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $tpl }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $to }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $by }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                            {{ $when ? \Illuminate\Support\Carbon::parse($when)->format('d.m.Y H:i') : '—' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-14 text-center">
                                            <div class="text-sm font-semibold text-gray-900">Nema istorije</div>
                                            <div class="mt-1 text-sm text-gray-500">Kad pošalješ prvi email, pojaviće se ovde.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-100 text-xs text-gray-500">
                        Kad ubaciš queue/job (npr. `ProcessIncomingEvent` ili poseban job za mail), ovde možeš da prikažeš i status (queued/sent/failed).
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>