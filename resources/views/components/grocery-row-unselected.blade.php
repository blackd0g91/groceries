@props(['grocery', 'position'])

<tr class="{{ $position % 2 == 0 ? 'even' : 'odd' }}">
    <td><x-grocery-link :grocery="$grocery" /></td>
    @for ($i = 1; $i <= 5; $i++)
        <td><x-select-button :grocery="$grocery" :value="$i" hide /></td>
    @endfor
</tr>
