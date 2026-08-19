@props([
    'size' => 6,
    'numeric' => false,
    'hide' => false,
    'hideType' => 'disc',
])

<div
    x-data="{
        value: @entangle($attributes->wire('model')),
        inputs: Array({{ $size }}).fill(''),
        init() {
            this.inputs = this.value ? this.value.split('').concat(Array({{ $size }}).fill('')).slice(0, {{ $size }}) : Array({{ $size }}).fill('');
        },
        handleInput(index, event) {
            const val = event.target.value;
            if ({{ $numeric ? 'true' : 'false' }} && val && isNaN(val)) {
                event.target.value = '';
                return;
            }
            this.inputs[index] = val;
            this.value = this.inputs.join('');
            if (val && index < {{ $size - 1 }}) {
                this.$refs['pin-' + (index + 1)]?.focus();
            }
        },
        handleKeydown(index, event) {
            if (event.key === 'Backspace' && !this.inputs[index] && index > 0) {
                this.inputs[index - 1] = '';
                this.value = this.inputs.join('');
                this.$refs['pin-' + (index - 1)]?.focus();
            }
        },
        handlePaste(event) {
            const paste = (event.clipboardData || window.clipboardData).getData('text');
            for (let i = 0; i < {{ $size }}; i++) {
                this.inputs[i] = paste[i] || '';
            }
            this.value = this.inputs.join('');
            event.preventDefault();
        }
    }"
    class="flex gap-2"
>
    @foreach(range(0, $size - 1) as $index)
        <input
            x-ref="pin-{{ $index }}"
            type="{{ $hide ? 'password' : ($numeric ? 'number' : 'text') }}"
            maxlength="1"
            x-model="inputs[{{ $index }}]"
            @input="handleInput({{ $index }}, $event)"
            @keydown="handleKeydown({{ $index }}, $event)"
            @paste="handlePaste($event)"
            class="input input-bordered w-10 h-12 text-center text-lg"
            {{ $index === 0 ? 'autofocus' : '' }}
        />
    @endforeach
</div>
