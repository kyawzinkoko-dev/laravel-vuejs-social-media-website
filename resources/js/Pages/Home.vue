<template>

    <Head title="Posts" />
    <AuthenticatedLayout>
        <div class="grid lg:grid-cols-12 col-span-1 gap-3 p-4 h-full">
            <div class="lg:col-span-3  lg:order-1 h-full overflow-hidden">
                <GroupList :groups="groups" />
            </div>
            <div class="lg:col-span-3 gap-6 lg:order-3 h-full overflow-auto">
                <FriendItem :followings = "followings" />
            </div>
            <div class="lg:col-span-6 lg:order-2 h-full overflow-hidden flex flex-col">
               <CreatePost />
                <PostList :posts="posts.data" class="flex-1" />
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
    groups:{type:Array},
    followings : { type: Array}
})

</script>

<style lang="scss" scoped></style>