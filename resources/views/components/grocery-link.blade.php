@props(['grocery'])

<span><a href="{{ $grocery->tausteSearchUrl() }}" target="_blank" rel="noopener noreferrer">{{ $grocery->name }}</a></span>
