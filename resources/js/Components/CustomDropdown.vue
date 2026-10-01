<script setup>
import { ref, onMounted, onUnmounted } from "vue";

defineProps({
  modelValue: [String, Number],
  options: Array,
  placeholder: { type: String, default: 'Select option' },
  hoverClass: { type: String, default: 'hover:bg-brand-400 hover:text-white' },
  focusClass: { type: String, default: 'focus:border-brand-400 focus:ring-brand-400' },
  disabled: { type: Boolean, default: false } // Naya prop
})

const emit = defineEmits(["update:modelValue", "change"]);
const isOpen = ref(false);

const selectOption = (option) => {
    emit("update:modelValue", option.value);
    emit("change", option.value);
    isOpen.value = false;
};

const closeDropdown = (e) => {
    if (!e.target.closest(".custom-dropdown")) {
        isOpen.value = false;
    }
};

onMounted(() => window.addEventListener("click", closeDropdown));
onUnmounted(() => window.removeEventListener("click", closeDropdown));
</script>

<template>
    <div class="relative custom-dropdown">
        <!-- Button ke andar `:class="[..., focusClass]"` laga diya hai -->
        <button  type="button" :disabled="disabled"@click="!disabled && (isOpen = !isOpen)"
                :class="[ 'w-full flex items-center justify-between rounded-xl border border-slate-200 text-sm py-3 px-4 bg-white text-slate-700 shadow-sm focus:ring-2 focus:outline-none transition-all',
               disabled ? 'opacity-60 cursor-not-allowed bg-gray-50/50' : 'cursor-pointer',focusClass]">
               <span>
                     {{options.find((o) => o.value === modelValue)?.label || placeholder }}
                </span>
             <svg class="h-4 w-4 text-slate-500 transition-transform"
                :class="{ 'rotate-180': isOpen }"  fill="none" stroke="currentColor" viewBox="0 0 24 24" >
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
             </svg>
        </button>

        <ul  v-if="isOpen"
            class="absolute z-50 mt-2 w-full bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden py-1 max-h-60 overflow-y-auto">
            <li v-for="option in options"
                :key="option.value"
                @click="selectOption(option)"
                :class="[  'px-4 py-2.5 text-sm text-slate-700 cursor-pointer transition-colors',hoverClass,]"  >
                {{ option.label }}
            </li>
        </ul>
    </div>
</template>
