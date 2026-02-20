<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Unesite PIN za pristup Call Center aplikaciji.
    </div>

    <form method="POST" action="{{ route('pin.login.store') }}">
        @csrf

        <div>
            <x-input-label for="pin" value="PIN" />
            <x-text-input id="pin" name="pin" type="password" inputmode="numeric" maxlength="4"
                class="mt-1 block w-full"
                autofocus
                autocomplete="one-time-code"
            />
            <x-input-error :messages="$errors->get('pin')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Uloguj se
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>