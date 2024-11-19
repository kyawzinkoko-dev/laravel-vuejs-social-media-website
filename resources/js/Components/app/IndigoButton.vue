<template>
    <button
        :type="type"
        class="bg-indigo-600 flex justify-center items-center gap-1 relative hover:bg-indigo-500 text-sm font-semibold py-2 px-3 text-white rounded transition-colors shadow-sm focus-visible:outline"
    >
        <slot></slot>
    </button>
</template>

<script setup>
defineProps({
    type: {
        type: String,
        default: "button",
    },
});
</script>

<style lang="scss" scoped></style>
