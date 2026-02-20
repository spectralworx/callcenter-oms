@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl',
    'title' => null,
])

@php
$widthClass = match ($maxWidth) {
    'sm'  => 'max-w-sm',
    'md'  => 'max-w-md',
    'lg'  => 'max-w-lg',
    'xl'  => 'max-w-xl',
    default => 'max-w-2xl',
};
@endphp

<div
    id="modal-{{ $name }}"
    role="dialog"
    aria-modal="true"
    class="{{ $show ? '' : 'hidden' }}"
    style="position:fixed; inset:0; z-index:50; display:{{ $show ? 'flex' : 'none' }}; align-items:center; justify-content:center; padding:1rem;"
>
    {{-- Backdrop --}}
    <div
        class="modal-backdrop"
        onclick="closeModal('{{ $name }}')"
        style="position:fixed; inset:0; background:rgba(15,23,42,0.45); backdrop-filter:blur(2px);"
    ></div>

    {{-- Panel --}}
    <div
        class="modal-panel {{ $widthClass }}"
        style="position:relative; z-index:10; background:#fff; border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,0.15); width:100%; max-height:90vh; overflow-y:auto;"
    >
        @if ($title)
            <div style="display:flex; align-items:center; justify-content:space-between; padding:1.1rem 1.5rem; border-bottom:1px solid #f1f5f9;">
                <div style="font-size:1rem; font-weight:800; color:#1e293b; font-family:'Figtree',sans-serif;">
                    {{ $title }}
                </div>
                <button
                    type="button"
                    onclick="closeModal('{{ $name }}')"
                    style="background:none; border:none; cursor:pointer; font-size:1.25rem; color:#94a3b8; line-height:1; padding:0.2rem; border-radius:6px; transition:color 0.1s;"
                    onmouseenter="this.style.color='#334155'"
                    onmouseleave="this.style.color='#94a3b8'"
                >✕</button>
            </div>
        @endif

        <div style="padding:1.5rem; font-family:'Figtree',sans-serif;">
            {{ $slot }}
        </div>
    </div>
</div>

@once
<style>
    .modal-panel { animation: modalIn 0.18s ease; }
    @keyframes modalIn {
        from { opacity:0; transform:translateY(8px) scale(0.98); }
        to   { opacity:1; transform:translateY(0) scale(1); }
    }
</style>
<script>
    function openModal(name) {
        const el = document.getElementById('modal-' + name);
        if (!el) return;
        el.classList.remove('hidden');
        el.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        const first = el.querySelector('button:not([onclick*="closeModal"]), input:not([type=hidden]), textarea, select');
        if (first) setTimeout(() => first.focus(), 50);
    }
    function closeModal(name) {
        const el = document.getElementById('modal-' + name);
        if (!el) return;
        el.classList.add('hidden');
        el.style.display = 'none';
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;
        document.querySelectorAll('[id^="modal-"]:not(.hidden)').forEach(el => {
            const name = el.id.replace('modal-', '');
            closeModal(name);
        });
    });
</script>
@endonce
