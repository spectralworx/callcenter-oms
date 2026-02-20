<nav class="bg-gray-950 border-b border-gray-800 px-8 py-0" x-data="{ open: false }">
    <div class="flex items-center justify-between h-16">

        {{-- Logo + desktop nav --}}
        <div class="flex items-center gap-2">
            <a href="{{ route('app.home') }}" class="text-white font-black text-xl tracking-tight">📞 OMS</a>

            <div class="hidden lg:flex items-center gap-1 ml-6">
                <a href="{{ route('app.call-centar') }}"
                   class="px-4 py-2 rounded-xl text-base font-bold transition-colors
                   {{ request()->is('app/call-centar*') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                    🔍 Call Centar
                </a>
                <a href="{{ route('app.pickup') }}"
                   class="px-4 py-2 rounded-xl text-base font-bold transition-colors
                   {{ request()->is('app/licno*') ? 'bg-emerald-700 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                    📦 Pickup
                </a>
                <a href="{{ route('app.send') }}"
                   class="px-4 py-2 rounded-xl text-base font-bold transition-colors
                   {{ request()->is('app/posalji*') ? 'bg-amber-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                    ✉️ Pošalji
                </a>
                <a href="{{ route('app.print') }}"
                   class="px-4 py-2 rounded-xl text-base font-bold transition-colors
                   {{ request()->is('app/stampa*') ? 'bg-sky-700 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                    🖨️ Štampa
                </a>
            </div>
        </div>

        {{-- User + logout --}}
        <div class="hidden lg:flex items-center gap-3">
            <span class="text-gray-400 text-base font-medium">{{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="px-4 py-2 rounded-xl bg-gray-800 hover:bg-gray-700 text-gray-300 text-base font-bold transition-colors">
                    Odjavi se
                </button>
            </form>
        </div>

        {{-- Mobile burger --}}
        <button @click="open = !open" class="lg:hidden text-gray-400 hover:text-white p-2">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path :class="{'hidden': open}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                <path :class="{'hidden': !open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Mobile menu --}}
    <div :class="{'block': open, 'hidden': !open}" class="hidden lg:hidden pb-4 space-y-2">
        <a href="{{ route('app.call-centar') }}"
           class="block rounded-xl px-4 py-4 text-xl font-bold transition-colors
           {{ request()->is('app/call-centar*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            🔍 Call Centar
        </a>
        <a href="{{ route('app.pickup') }}"
           class="block rounded-xl px-4 py-4 text-xl font-bold transition-colors
           {{ request()->is('app/licno*') ? 'bg-emerald-700 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            📦 Pickup
        </a>
        <a href="{{ route('app.send') }}"
           class="block rounded-xl px-4 py-4 text-xl font-bold transition-colors
           {{ request()->is('app/posalji*') ? 'bg-amber-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            ✉️ Pošalji
        </a>
        <a href="{{ route('app.print') }}"
           class="block rounded-xl px-4 py-4 text-xl font-bold transition-colors
           {{ request()->is('app/stampa*') ? 'bg-sky-700 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            🖨️ Štampa
        </a>
        <div class="pt-2 border-t border-gray-800 flex items-center justify-between">
            <span class="text-gray-400 text-base">{{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-white text-base font-bold">Odjavi se</button>
            </form>
        </div>
    </div>
</nav>
