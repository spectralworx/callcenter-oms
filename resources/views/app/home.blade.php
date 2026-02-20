<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Call Center OMS
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="{{ route('app.call-centar') }}" class="bg-white shadow-sm rounded-lg p-6 hover:shadow transition">
                    <div class="text-lg font-semibold text-gray-900">Call Centar</div>
                    <div class="mt-2 text-sm text-gray-600">Pretraga porudžbina, detalji, otkazivanje.</div>
                </a>

                <a href="{{ route('app.pickup') }}" class="bg-white shadow-sm rounded-lg p-6 hover:shadow transition">
                    <div class="text-lg font-semibold text-gray-900">Lično preuzimanje</div>
                    <div class="mt-2 text-sm text-gray-600">Sken QR/termalni broj → completed.</div>
                </a>

                <a href="{{ route('app.send') }}" class="bg-white shadow-sm rounded-lg p-6 hover:shadow transition">
                    <div class="text-lg font-semibold text-gray-900">Pošalji porudžbinu</div>
                    <div class="mt-2 text-sm text-gray-600">JOICO forma → Resend email.</div>
                </a>

                <a href="{{ route('app.print') }}" class="bg-white shadow-sm rounded-lg p-6 hover:shadow transition">
                    <div class="text-lg font-semibold text-gray-900">Štampa</div>
                    <div class="mt-2 text-sm text-gray-600">Neštampane, bulk, istorija.</div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>