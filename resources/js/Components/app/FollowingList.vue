<template>
    <a :href="route('profile',user)" class="flex items-center gap-3 mb-3 dark:hover:bg-gray-700 text-gray-900 dark:text-gray-300  hover:bg-gray-100 py-2 px-3 rounded">
        <img :src="user.avatar_url" class="rounded-full w-[40px]" />
        <div>
            <h3 class="font-semibold text-lg">{{ user.name }}</h3>
            
        </div>
    </a>
</template>

<script setup>
   defineProps({
    user:Object
   })
</script>

<style lang="scss" scoped>

</style>