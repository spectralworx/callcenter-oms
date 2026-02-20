@props([
    'id',
    'title' => '',
    'open' => false,
])

<div
    id="{{ $id }}"
    class="fixed inset-0 z-50 {{ $open ? '' : 'hidden' }}"
    aria-hidden="{{ $open ? 'false' : 'true' }}"
>
    <div class="absolute inset-0 bg-black/40" data-modal-close></div>

    <div class="relative mx-auto mt-16 w-full max-w-3xl px-4">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                <button type="button" class="text-gray-500 hover:text-gray-700" data-modal-close>
                    ✕
                </button>
            </div>

            <div class="px-5 py-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>