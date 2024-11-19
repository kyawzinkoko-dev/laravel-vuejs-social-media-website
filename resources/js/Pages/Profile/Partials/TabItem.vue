<template>
    <button
        :class="[
            ' focus:outline-none outline-none focus:ring-0 px-4 py-2.5 text-sm ',

            selected
                ? ' border-b-2 border-blue-500 text-blue-500 '
                : 'text-gray-600',
        ]"
    >
        {{ text }}
    </button>
</template>

<script setup>
defineProps({
    selected: Boolean,
    text: String,
});
</script>

<style lang="scss" scoped></style>
