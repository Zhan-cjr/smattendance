@props([
    'name',
    'label',
    'data',
    'key',
    'textShow',
    'selected' => '',
    'upperCase' => false,
    'select2' => '',
    'showKey' => false,
    'hideLabel' => false,
    'placeholder' => null,
])

<div class="form-group mb-3">
    @if($label && !$hideLabel)
        <label for="{{ $name }}" class="form-label" style="font-weight: 600;">{{ $label }}</label>
    @endif
    <select name="{{ $name }}" id="{{ $name }}" class="form-select {{ $select2 }}">
        <option value="">{{ $placeholder ?? $label }}</option>
        @foreach ($data as $d)
            <option {{ $d->$key == $selected ? 'selected' : '' }} value="{{ $d->$key }}">
                {{ $showKey ? $d->$key . '-' : '' }}
                {{ $upperCase ? strtoupper(strtolower($d->$textShow)) : ucwords(strtolower($d->$textShow)) }}
            </option>
        @endforeach
    </select>
</div>
