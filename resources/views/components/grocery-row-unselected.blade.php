@props(['grocery'])

<li data-item data-name="{{ $grocery->name }}" class="flex items-center gap-3 px-4 py-2.5">
    <x-grocery-link :grocery="$grocery" />
    <form method="POST" action="{{ url('select/' . $grocery->id) }}" data-remove-row data-delta="1">
        @csrf
        <button type="submit" aria-label="Buy {{ $grocery->name }}"
                class="inline-flex shrink-0 cursor-pointer items-center gap-1 rounded-full bg-accent-soft px-3.5 py-1.5 text-sm font-medium text-accent transition-colors hover:bg-accent hover:text-accent-ink disabled:opacity-50">
            <x-icon name="plus" class="size-4" />
            Buy
        </button>
    </form>
</li>
