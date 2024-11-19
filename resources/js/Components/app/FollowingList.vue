<template>
    <div class="flex items-center gap-3 mb-3  hover:bg-gray-100 py-2 px-3 rounded">
        <img :src="image" class="rounded-full w-[40px]" />
        <div>
            <h3 class="font-semibold text-lg">{{ title }}</h3>
            
        </div>
    </div>
</template>

<script setup>
    defineProps({
        image:String,
        title:String
    })
</script>

<style lang="scss" scoped>

</style>