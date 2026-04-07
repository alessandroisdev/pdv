@props(['name', 'label', 'type' => 'text', 'placeholder' => '', 'value' => '', 'required' => false, 'icon' => null])

<div style="margin-bottom: 1rem;">
    @if($label)
        <label for="{{ $name }}" class="fw-semibold text-primary" style="display: block; margin-bottom: 0.5rem;">
            {{ $label }} @if($required) <span class="text-contrast">*</span> @endif
        </label>
    @endif
    
    <div style="position: relative;">
        @if($icon)
            <div style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #64748b;">
                {!! $icon !!}
            </div>
        @endif

        <input type="{{ $type }}" 
               name="{{ $name }}" 
               id="{{ $name }}"
               value="{{ old($name, $value) }}"
               placeholder="{{ $placeholder }}"
               {{ $required ? 'required' : '' }}
               {{ $attributes->merge(['class' => 'form-control']) }}
               style="width: 100%; padding: 0.75rem {{ $icon ? '2.5rem' : '1rem' }}; border: 1px solid #e2e8f0; border-radius: 6px; outline: none; transition: border-color 0.2s;">
    </div>

    @error($name) 
        <span style="color: #ef4444; font-size: 0.8rem; display: block; margin-top: 0.25rem;">{{ $message }}</span> 
    @enderror
</div>
