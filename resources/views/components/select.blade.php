@props([
    'name' => '',
    'options' => [],
    'placeholder' => '-- Pilih Options --',
    'selected' => '',
    'bind' => null, // e.g. 'formData.parent_id'
    'class' => '',
    'searchable' => false,
])

@php
    // Format options array safely into [{value: '...', label: '...'}]
    $formattedOptions = [];
    if (is_array($options)) {
        foreach ($options as $key => $opt) {
            if (is_array($opt) && isset($opt['value'])) {
                $formattedOptions[] = ['value' => (string)$opt['value'], 'label' => (string)$opt['label']];
            } else {
                $formattedOptions[] = ['value' => (string)$key, 'label' => (string)$opt];
            }
        }
    }
@endphp

<div x-data="customSelectComponent(@js($formattedOptions), @js($placeholder), @js($selected), @js($bind))"
     @click.outside="open = false"
     class="relative w-full {{ $class }}">

    <!-- Hidden Native Input for standard HTML Form Submit -->
    @if($name)
        <input type="hidden" name="{{ $name }}" :value="selected">
    @endif

    <!-- Trigger Button -->
    <button 
        type="button" 
        @click="open = !open" 
        class="w-full flex items-center justify-between p-2 text-xs bg-slate-50 border border-slate-300 text-slate-900 hover:bg-white focus:bg-white focus:border-blue-500 focus:outline-none transition-colors cursor-pointer">
        
        <span x-text="selectedLabel" :class="(!selected && selected !== 0) ? 'text-slate-400' : 'text-slate-900 font-medium'"></span>
        
        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200"
           :class="open ? 'rotate-180 text-blue-600' : ''"></i>
    </button>

    <!-- Dropdown Options List -->
    <div 
        x-show="open" 
        x-cloak
        x-transition:enter="transition ease-out duration-150 transform"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100 transform"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
        style="display: none;"
        class="absolute left-0 right-0 top-full mt-1 z-50 bg-white border border-slate-300 max-h-60 overflow-y-auto py-1 text-xs">

        @if($searchable)
            <div class="p-1.5 border-b border-slate-200">
                <input 
                    type="text" 
                    x-model="search" 
                    placeholder="Cari..." 
                    class="w-full px-2 py-1 bg-slate-50 border border-slate-200 text-xs focus:outline-none focus:border-blue-500">
            </div>
        @endif

        <template x-for="(opt, idx) in options" :key="idx">
            <button 
                type="button"
                x-show="!search || opt.label.toLowerCase().includes(search.toLowerCase())"
                @click="selectOption(opt.value)"
                class="w-full flex items-center justify-between px-3 py-2 text-left transition-colors cursor-pointer"
                :class="String(selected) === String(opt.value) ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-slate-700 hover:bg-slate-50'">
                
                <span x-text="opt.label"></span>
            </button>
        </template>
    </div>
</div>

@once
<script>
    function customSelectComponent(options, placeholder, initialSelected, bindPath) {
        return {
            open: false,
            selected: initialSelected ? String(initialSelected) : '',
            options: options || [],
            search: '',

            init() {
                if (bindPath) {
                    this.$watch(bindPath, (val) => {
                        this.selected = (val !== null && val !== undefined) ? String(val) : '';
                    });
                    try {
                        const boundVal = this.$evaluate(bindPath);
                        if (boundVal !== undefined && boundVal !== null) {
                            this.selected = String(boundVal);
                        }
                    } catch (e) {}
                }
            },

            get selectedLabel() {
                const currentVal = String(this.selected ?? '');
                const found = this.options.find(o => String(o.value) === currentVal);
                return found ? found.label : placeholder;
            },

            selectOption(val) {
                this.selected = val;
                this.open = false;
                if (bindPath) {
                    try {
                        const parts = bindPath.split('.');
                        let target = this;
                        for (let i = 0; i < parts.length - 1; i++) {
                            target = target[parts[i]];
                        }
                        target[parts[parts.length - 1]] = val;
                    } catch (e) {}
                }
                this.$dispatch('input', val);
                this.$dispatch('change', val);
            }
        };
    }
</script>
@endonce
