@props(['grocery', 'value', 'hide' => false])

<form method="POST" action="{{ url('select/' . $grocery->id) }}" @if ($hide) select-and-hide @endif>
    @csrf
    <input type="hidden" name="value" value="{{ $value }}">
    <button type="submit">{{ $value }}</button>
</form>
