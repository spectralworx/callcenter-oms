<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Lično preuzimanje
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Sken QR / termalni broj → validacija → “completed”.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">
                    <div class="font-semibold text-sm">Uspešno</div>
                    <div class="text-sm">{{ session('status') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
                    <div class="font-semibold text-sm">Greška</div>
                    <div class="text-sm">Proveri unos i pokušaj ponovo.</div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                {{-- Scan panel --}}
                <div class="lg:col-span-5 bg-white shadow-sm rounded-lg border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <div class="text-sm font-semibold text-gray-900">Sken / unos</div>
                        <div class="text-xs text-gray-500">QR sadržaj ili termalni broj (npr. #12345).</div>
                    </div>

                    <div class="p-6 space-y-4">
                        <form method="POST" action="{{ url()->current() }}" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kod</label>
                                <input
                                    name="code"
                                    value="{{ old('code') }}"
                                    autofocus
                                    autocomplete="off"
                                    placeholder="Skeniraj ili upiši kod…"
                                    class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                <x-input-error :messages="$errors->get('code')" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                                    Potvrdi preuzimanje
                                </button>

                                <button type="button"
                                    onclick="document.querySelector('input[name=code]').value=''; document.querySelector('input[name=code]').focus();"
                                    class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    Očisti
                                </button>
                            </div>
                        </form>

                        <div class="rounded-lg bg-gray-50 border border-gray-100 p-4 text-sm text-gray-600">
                            <div class="font-semibold text-gray-900 text-sm">Saveti</div>
                            <ul class="mt-2 list-disc ps-5 space-y-1 text-sm">
                                <li>Scanner obično šalje “Enter” na kraju — forma je spremna.</li>
                                <li>Ako je kod “#12345”, možeš slati u istom formatu.</li>
                                <li>Posle uspeha, fokus ostaje na inputu za sledeći sken.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Recent pickups --}}
                <div class="lg:col-span-7 bg-white shadow-sm rounded-lg border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <div class="text-sm font-semibold text-gray-900">Poslednja preuzimanja</div>
                            <div class="text-xs text-gray-500">Lista poslednjih potvrđenih preuzimanja.</div>
                        </div>

                        <a href="{{ route('app.call-centar') }}"
                           class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Otvori Call Centar
                        </a>
                    </div>

                    @php $recent = $recent ?? collect(); @endphp

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Porudžbina</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kupac</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Vreme</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse ($recent as $r)
                                    @php
                                        $number = $r->number ?? $r['number'] ?? ($r->id ?? $r['id'] ?? '—');
                                        $cust = $r->customer_name ?? $r['customer_name'] ?? '—';
                                        $when = $r->picked_up_at ?? $r['picked_up_at'] ?? ($r->updated_at ?? $r['updated_at'] ?? null);
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-semibold text-gray-900">#{{ $number }}</div>
                                            <div class="text-xs text-gray-500">Lično preuzimanje</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $cust }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-green-100 text-green-800">
                                                COMPLETED
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                            {{ $when ? \Illuminate\Support\Carbon::parse($when)->format('d.m.Y H:i') : '—' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-14 text-center">
                                            <div class="text-sm font-semibold text-gray-900">Još nema preuzimanja</div>
                                            <div class="mt-1 text-sm text-gray-500">Kreni sa skeniranjem da se pojave stavke u istoriji.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-100 text-xs text-gray-500">
                        Tipično ovde ide i audit (ko je potvrdio) + filter po datumu, kad budeš hteo.
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        // UX: posle submit-a fokus ostaje na inputu (ako browser ne uradi sam)
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.querySelector('input[name="code"]');
            if (input) input.focus();
        });
    </script>
</x-app-layout>