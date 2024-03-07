@props(['disabled' => false, 'options' => [], 'default' => ''])

<select {{ $disabled ? 'disabled' : '' }}
        {!! $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) !!}>
    <option value="" {{ $default === '' ? 'selected' : '' }}></option>
    @foreach($options as $value => $label)
        <option value="{{ $value }}" {{ (string)$default === (string)$value ? 'selected' : '' }}>{{ $label }}</option>
    @endforeach
</select>
