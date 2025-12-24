@props([
  'name',
  'value' => null,
  'placeholder' => 'Selecione...',
  'options' => [], // ['valor' => 'Label']
])

@php
  $currentValue = old($name, $value);
  $currentLabel = $options[$currentValue] ?? $placeholder;
  $id = 'ui_select_' . md5($name . ($attributes->get('id') ?? ''));
@endphp

<div class="relative" data-ui-select id="{{ $id }}">
  <input type="hidden" name="{{ $name }}" value="{{ $currentValue }}" data-ui-select-value>

  <button type="button"
    class="w-full h-11 px-4 pr-10 rounded-xl bg-white border border-slate-200 text-left font-semibold text-slate-900
           focus:outline-none focus:ring-4 focus:ring-[#448ccb]/20 focus:border-[#23366f] transition"
    data-ui-select-trigger>
    <span class="{{ $currentValue ? 'text-slate-900' : 'text-slate-400' }}" data-ui-select-label>
      {{ $currentLabel }}
    </span>

    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
      <i class="fas fa-chevron-down text-xs"></i>
    </span>
  </button>

  <div class="absolute z-[9999] mt-2 w-full rounded-xl border border-slate-200 bg-white shadow-xl hidden overflow-hidden"
       data-ui-select-panel>
    <div class="max-h-64 overflow-auto">
      @foreach($options as $optValue => $optLabel)
        <button type="button"
          class="w-full px-4 py-3 text-left hover:bg-slate-50 font-semibold text-slate-700 flex items-center justify-between"
          data-ui-select-option
          data-value="{{ $optValue }}">
          <span>{{ $optLabel }}</span>
          @if((string)$optValue === (string)$currentValue)
            <i class="fas fa-check text-[#23366f] text-xs"></i>
          @endif
        </button>
      @endforeach
    </div>
  </div>
</div>