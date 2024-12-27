<template>
    <AuthenticatedLayout>
        <div  v-if="!search.startsWith('#')"class="grid p-3 grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
            <div class="p-3 bg-white rounded shadow dark:bg-gray-800 dark:text-gray-300 text-gray-900">
                <h2 class="text-lg font-bold">Users</h2>
                <div v-if="users.length">
                    <UserListItem v-for="user of users" :user="user" />
                </div>
                <div v-else class="text-center py-8">No user result for  " {{ search }} "</div>
            </div>
            <div class="p-3 bg-white rounded shadow dark:bg-gray-800 dark:text-gray-300 text-gray-900">
                <h2 class="text-lg font-bold">Group</h2>
                <GroupItem v-if="groups.length" v-for="group of groups" :group="group" />
                <div v-else class="py-8 text-center">
                    No group result for  "{{ search }}"
                </div>
            </div>
        </div>
        <div class="p-3">
            <h2 class="text-lg font-bold">Posts</h2>
            <PostList v-if="posts.data.length" :posts="posts.data" />
            <div v-else>You don't have any post</div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import GroupItem from "@/Components/app/GroupItem.vue";
import PostList from "@/Components/app/PostList.vue";
import UserListItem from "@/Components/app/UserListItem.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    users: Array,
    groups: Array,
    posts: Object,
    search:{type:String,default:null}
});
console.log(props)
</script>

<style lang="scss" scoped></style>
