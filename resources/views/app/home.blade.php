<x-app-layout>
    <div class="min-h-screen bg-gray-950 flex flex-col">

        {{-- Header --}}
        <div class="flex items-center justify-between px-8 py-5 border-b border-gray-800">
            <span class="text-white text-2xl font-black tracking-tight">📞 Call Center OMS</span>
            <span class="text-gray-400 text-lg font-medium">{{ Auth::user()->name }}</span>
        </div>

        {{-- Main grid --}}
        <div class="flex-1 grid grid-cols-2 gap-6 p-8">

            <a href="{{ route('app.call-centar') }}"
               class="group flex flex-col items-center justify-center gap-5 rounded-3xl bg-indigo-600 hover:bg-indigo-500 active:scale-95 transition-all duration-150 shadow-2xl min-h-[260px]">
                <span class="text-7xl">🔍</span>
                <span class="text-white text-3xl font-black tracking-tight">Call Centar</span>
                <span class="text-indigo-200 text-lg font-medium">Pretraži porudžbine</span>
            </a>

            <a href="{{ route('app.pickup') }}"
               class="group flex flex-col items-center justify-center gap-5 rounded-3xl bg-emerald-600 hover:bg-emerald-500 active:scale-95 transition-all duration-150 shadow-2xl min-h-[260px]">
                <span class="text-7xl">📦</span>
                <span class="text-white text-3xl font-black tracking-tight">Lično preuzimanje</span>
                <span class="text-emerald-200 text-lg font-medium">Sken → Completed</span>
            </a>

            <a href="{{ route('app.send') }}"
               class="group flex flex-col items-center justify-center gap-5 rounded-3xl bg-amber-500 hover:bg-amber-400 active:scale-95 transition-all duration-150 shadow-2xl min-h-[260px]">
                <span class="text-7xl">✉️</span>
                <span class="text-white text-3xl font-black tracking-tight">Pošalji porudžbinu</span>
                <span class="text-amber-100 text-lg font-medium">Resend email kupcu</span>
            </a>

            <a href="{{ route('app.print') }}"
               class="group flex flex-col items-center justify-center gap-5 rounded-3xl bg-sky-600 hover:bg-sky-500 active:scale-95 transition-all duration-150 shadow-2xl min-h-[260px]">
                <span class="text-7xl">🖨️</span>
                <span class="text-white text-3xl font-black tracking-tight">Štampa</span>
                <span class="text-sky-200 text-lg font-medium">Neštampane & bulk</span>
            </a>

        </div>

        {{-- Footer logout --}}
        <div class="px-8 py-4 border-t border-gray-800 flex justify-end">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="text-gray-500 hover:text-gray-300 text-base font-medium transition-colors px-4 py-2">
                    Odjavi se
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
