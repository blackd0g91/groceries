@props(['grocery'])

<li data-item data-name="{{ $grocery->name }}" data-undo-action="{{ url('select/' . $grocery->id) }}" class="flex items-center gap-3 px-4 py-2.5">
    <x-grocery-link :grocery="$grocery" />
    <form method="POST" action="{{ url('trash/' . $grocery->id) }}" data-remove-row data-delta="-1">
        @csrf
        <button type="submit" aria-label="Remove {{ $grocery->name }} from list" title="Remove from list"
                class="grid size-9 shrink-0 cursor-pointer place-items-center rounded-full border border-line text-muted transition-colors hover:border-accent hover:bg-accent-soft hover:text-accent disabled:opacity-50">
            <x-icon name="check" class="size-5" />
        </button>
    </form>
</li>
