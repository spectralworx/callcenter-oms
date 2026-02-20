<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Lično preuzimanje (SCAN)
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6 space-y-4">

                <div class="text-sm text-gray-700">
                    Unesi / skeniraj barkod (termal kod). Sistem otvara porudžbinu i prikazuje pickup modal.
                </div>

                <form method="POST" action="{{ route('app.pickup.scan') }}" class="space-y-3">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="code">Barkod / Termal kod</label>
                        <input
                            id="code"
                            name="code"
                            type="text"
                            value="{{ old('code') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            autofocus
                            autocomplete="off"
                        />
                        @error('code')
                            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="w-full inline-flex justify-center items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                    >
                        Otvori porudžbinu
                    </button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>