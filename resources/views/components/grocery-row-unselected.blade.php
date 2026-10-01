@props(['grocery', 'position'])

<tr class="{{ $position % 2 == 0 ? 'even' : 'odd' }}">
    <td><x-grocery-link :grocery="$grocery" /></td>
    <td>
        <form method="POST" action="{{ url('select/' . $grocery->id) }}" select-and-hide>
            @csrf
            <button type="submit">Buy</button>
        </form>
    </td>
</tr>
