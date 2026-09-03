@props([
    'icon' => '',
    'name' => '',
    'label' => '',
    'value' => '',
    'readonly' => false,
    'type' => 'text',
    'align' => '',
    'disabled' => false,
    'money' => false,
    'numberFormat' => false,
    'datepicker' => '',
    'placeholder' => null,
    'maxlength' => null,
    'min' => null,
    'max' => null,
    'required' => false,
    'hideLabel' => false,
    'id' => null,
])
<div class="form-group mb-3">
    @if ($label && !$hideLabel)
        <label for="{{ $id ?? $name }}" class="form-label" style="font-weight: 600;">
            {{ $label }}
            @if ($required && !$readonly && !$disabled)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    <div class="input-group input-group-merge">
        <span class="input-group-text" id="basic-addon-search31"><i class="{{ $icon }}"></i></span>
        <input type="{{ $type }}"
            class="form-control {{ $money ? 'money' : '' }} {{ $numberFormat ? 'number-separator' : '' }} {{ $datepicker }}"
            id="{{ $id ?? $name }}" name="{{ $name }}" placeholder="{{ $placeholder ?? $label }}" {{ $readonly ? 'readonly' : '' }}
            {{ $disabled ? 'disabled' : '' }} autocomplete="off" aria-autocomplete="none" value="{{ $value }}"
            style="text-align: {{ $align }}" {{ $maxlength ? 'maxlength=' . $maxlength : '' }} {{ $min !== null ? 'min=' . $min : '' }}
            {{ $max !== null ? 'max=' . $max : '' }} {{ $required && !$readonly && !$disabled ? 'required' : '' }}>
    </div>
</div>
