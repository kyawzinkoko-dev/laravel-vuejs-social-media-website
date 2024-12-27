<script setup>
import {onMounted, ref, watch} from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        required: false,
    },
    placeholder: String,
    autoResize: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

watch(() => props.modelValue, () => {
    setTimeout(() => {
       
        adjustHeight()
    }, 10)
})

defineExpose({focus: () => input.value.focus()});

function onInputChange($event) {
    emit('update:modelValue', $event.target.value)
    adjustHeight();
}

function adjustHeight() {
   
    if (props.autoResize) {
    
        input.value.style.height = 'auto';
        input.value.style.height = (input.value.scrollHeight + 1) + 'px';
    }
}

onMounted(() => {
    adjustHeight()
})
</script>
<template>
    <textarea
        class="border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm placeholder:text-gray-400"
        :value="modelValue"
        @input="onInputChange"
        :placeholder="placeholder"
        ref="input"
    ></textarea>
</template>
