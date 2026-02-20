<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
    <x-nav-link :href="route('app.home')" :active="request()->is('app')">
        Home
    </x-nav-link>

    <x-nav-link :href="route('app.call-centar')" :active="request()->is('app/call-centar')">
        Call Centar
    </x-nav-link>

    <x-nav-link :href="route('app.pickup')" :active="request()->is('app/licno-preuzimanje')">
        Lično preuzimanje
    </x-nav-link>

    <x-nav-link :href="route('app.send')" :active="request()->is('app/posalji-porudzbinu')">
        Pošalji porudžbinu
    </x-nav-link>

    <x-nav-link :href="route('app.print')" :active="request()->is('app/stampa')">
        Štampa
    </x-nav-link>
</div>