<template>
    <Link :href="route('group.profile',group.slug)" class="flex text-gray-900 items-center gap-3 mb-3 hover:bg-gray-100 dark:hover:bg-gray-800 dark:text-gray-300 py-2 px-3 rounded">
        <img :src="group.thumbnail_url" class="rounded-full w-[40px]" />
        <div class="flex-1">
           
            <div class="flex w-full flex-1 justify-between items-center">
                <h3 class="font-semibold text-lg">{{  group.name }}</h3>
                <p class="text-gray-900 dark:text-gray-300">{{ group.role==="admin" ?group.role:"" }}</p>
            </div>
            <div v-html="group.description" class="text-gray-700 dark:text-gray-300">
               
            </div>
        </div>
    </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const props =defineProps({
    group:Object
})
</script>

<style lang="scss" scoped></style>
