<template>

    <Head title="Posts" />
    <AuthenticatedLayout>
        <div class="grid lg:grid-cols-12 col-span-1 gap-3 h-full">
            <div class="lg:col-span-3 py-6 px-4 lg:order-1 h-full overflow-hidden">
                <GroupList :groups="groups" />
            </div>
            <div class="lg:col-span-3 gap-6 py-6 px-4 lg:order-3 h-full overflow-hidden">
                <FriendItem />
            </div>
            <div class="lg:col-span-6 px-4 py-6 flex flex-col lg:order-2 overflow-hidden h-full">
                <CreatePost />
                <PostList :posts="posts.data" />
            </div>
        </div>

    </AuthenticatedLayout>
</template>

<script setup>
import CreatePost from '@/Components/app/CreatePost.vue';
import FriendItem from '@/Components/app/FriendItem.vue';
import GroupList from '@/Components/app/GroupList.vue';
import PostList from '@/Components/app/PostList.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';

const page = usePage().props;

const prop= defineProps({
    posts: { type: Object,  },
    groups:{type:Array}
})

</script>

<style lang="scss" scoped></style>