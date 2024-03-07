@props([
    'disabled' => false, 
    'checked' => false, 
])

<input type="checkbox" {{ $checked ? 'checked' : '' }} {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => '...']) !!}>
