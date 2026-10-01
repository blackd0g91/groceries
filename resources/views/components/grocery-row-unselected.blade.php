@props(['grocery'])

<li data-item data-name="{{ $grocery->name }}" data-undo-action="{{ url('trash/' . $grocery->id) }}" class="flex items-center gap-2 px-4 py-2.5">
    <x-grocery-link :grocery="$grocery" />
    <a href="{{ url('groceries/' . $grocery->id . '/edit') }}" aria-label="Edit {{ $grocery->name }}" title="Edit"
       class="grid size-8 shrink-0 place-items-center rounded-full text-muted transition-colors hover:bg-ink/[0.06] hover:text-ink">
        <x-icon name="pencil" class="size-4" />
    </a>
    <form method="POST" action="{{ url('select/' . $grocery->id) }}" data-remove-row data-delta="1">
        @csrf
        <button type="submit" aria-label="Buy {{ $grocery->name }}"
                class="inline-flex shrink-0 cursor-pointer items-center gap-1 rounded-full bg-accent-soft px-3.5 py-1.5 text-sm font-medium text-accent transition-colors hover:bg-accent hover:text-accent-ink disabled:opacity-50">
            <x-icon name="plus" class="size-4" />
            Buy
        </button>
    </form>
</li>
